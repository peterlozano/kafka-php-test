<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Enqueue\RdKafka\RdKafkaConnectionFactory;


class ConsumerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('kafka:consumer')
            ->setDescription('Reads and prints messages from kafka.')
            ->addOption('topic', 't', InputOption::VALUE_REQUIRED, 'Receive messages from specific topic.', 'testing')
            ->addOption('pause', 'p', InputOption::VALUE_REQUIRED, 'Pause X microseconds between messages.', 0)
            ->addOption('auto-offset', 'ao', InputOption::VALUE_REQUIRED, 'Where to start reading from (latest, earliest).', 'latest');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connectionFactory = new RdKafkaConnectionFactory([
            'global' => [
                'group.id' => 1,
                'metadata.broker.list' => 'broker:29092',
                'enable.auto.commit' => 'true',
            ],
            'topic' => [
                'auto.offset.reset' => $input->getOption('auto-offset'),
            ],
            'commit_async' => true,
        ]);
        $context = $connectionFactory->createContext();
        $topic = $context->createTopic($input->getOption('topic'));
        $consumer = $context->createConsumer($topic);

        // Just to support ctrl+c to stop the process
        if (false !== extension_loaded('pcntl')) {
            pcntl_async_signals(true);
            pcntl_signal(SIGTERM, [$this, 'handleSignal']);
            pcntl_signal(SIGQUIT, [$this, 'handleSignal']);
            pcntl_signal(SIGINT, [$this, 'handleSignal']);
        }

        while (true) {
            try {
                $message = $consumer->receive();
            } catch (\Exception $e) {
                // ie: Malformed json.
                continue;
            }

            if ($message) {
                echo $message->getBody();
            }

            usleep($input->getOption('pause'));
        }

        return 0;
    }

    public function handleSignal(int $signal): void
    {
        exit(1);
    }
}
