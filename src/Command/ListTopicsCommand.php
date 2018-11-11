<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListTopicsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('kafka:topic:list')
            ->setDescription('List kafka topics.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kafka = new \RdKafka\Consumer();
        $kafka->addBrokers('broker:9092');

        $meta = $kafka->getMetadata(true, null, 1000);

        $topics = $meta->getTopics();

        $table = new Table($output);
        $table->setHeaders([
            'Topic', 'err', 'Partition', 'Leader', 'Replicas', 'Isrs'
        ]);
        foreach ($topics as $topic) {
            foreach ($topic->getPartitions() as $partition) {
                $table->addRow([
                    $topic->getTopic(),
                    $topic->getErr(),
                    $partition->getId(),
                    $partition->getLeader(),
                    $partition->getReplicas()->count(),
                    $partition->getIsrs()->count()
                ]);
            }
        }
        $table->render();

        return 0;
    }
}
