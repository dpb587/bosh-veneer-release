<?php

namespace Veneer\MarketplaceBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\DBAL\Connection;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class UpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('veneer:marketplace:update')
            ->addOption('releases', null, InputOption::VALUE_NONE, 'Update releases')
            ->addOption('stemcells', null, InputOption::VALUE_NONE, 'Update stemcells')
            ->addArgument('marketplace', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'The marketplace to update (or "all").')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $marketplaces = $input->getArgument('marketplace');

        if ([ 'all' ] == $marketplaces) {
            $marketplaces = $container->get('veneer_marketplace.marketplaces')->allKeys();
        }

        $updater = $container->get('veneer_marketplace.updater');

        $handler = new StreamHandler(fopen('php://stderr', 'a'));
        $handler->setFormatter(new LineFormatter("%datetime% %channel% %level_name% %message%\n", 'c'));

        foreach ($marketplaces as $marketplace) {
            $logger = new Logger($marketplace);
            $logger->pushHandler($handler);

            if ($input->getOption('releases')) {
                $updater->updateReleases($marketplace, $logger);
            }

            if ($input->getOption('stemcells')) {
                $updater->updateStemcells($marketplace, $logger);
            }
        }
    }
}