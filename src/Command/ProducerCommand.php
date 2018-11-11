<?php

namespace App\Command;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Enqueue\RdKafka\RdKafkaConnectionFactory;

class ProducerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('kafka:producer')
            ->setDescription('Send messages to kafka.')
            ->addOption('auto', null, InputOption::VALUE_NONE, 'Auto generate messages (uuids).')
            ->addOption('topic', 't', InputOption::VALUE_REQUIRED, 'Send messages to specific topic.', 'testing')
            ->addOption('pause', 'p', InputOption::VALUE_REQUIRED, 'Pause X microseconds between messages.', 0)
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit number of messages to X', -1);
    }

    private function getMessage(InputInterface $input)
    {
        $limit = $input->getOption('limit');

        while ($limit === -1 || $limit-- > 0) {
            if ($input->getOption('auto') === true) {
                yield Uuid::uuid4() . "\n";
            }
            else {
                yield fgets(STDIN);
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connectionFactory = new RdKafkaConnectionFactory([
            'global' => [
                'metadata.broker.list' => 'broker:29092',
            ]
        ]);
        $context = $connectionFactory->createContext();
        $topic = $context->createTopic($input->getOption('topic'));
        $producer = $context->createProducer();

        foreach ($this->getMessage($input) as $msg) {
            $message = $context->createMessage($msg);

            try {
                $producer->send($topic, $message);

                echo $msg;
            } catch (\Exception $e) {
                // noop
            }

            usleep($input->getOption('pause'));
        }

        return 0;
    }
}
