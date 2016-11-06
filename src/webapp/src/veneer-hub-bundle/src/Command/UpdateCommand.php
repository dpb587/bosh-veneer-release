<?php

namespace Veneer\HubBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class UpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('veneer:hub:update')
            ->addOption('releases', null, InputOption::VALUE_NONE, 'Update releases')
            ->addOption('stemcells', null, InputOption::VALUE_NONE, 'Update stemcells')
            ->addArgument('hub', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'The hub to update (or "all").')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $hubs = $input->getArgument('hub');

        if (['all'] == $hubs) {
            $hubs = $container->get('veneer_hub.hubs')->allKeys();
        }

        $updater = $container->get('veneer_hub.updater');

        $handler = new StreamHandler(fopen('php://stderr', 'a'));
        $handler->setFormatter(new LineFormatter("%datetime% %channel% %level_name% %message%\n", 'c'));

        foreach ($hubs as $hub) {
            $logger = new Logger($hub);
            $logger->pushHandler($handler);

            if ($input->getOption('releases')) {
                $updater->updateReleases($hub, $logger);
            }

            if ($input->getOption('stemcells')) {
                $updater->updateStemcells($hub, $logger);
            }
        }
    }
}
