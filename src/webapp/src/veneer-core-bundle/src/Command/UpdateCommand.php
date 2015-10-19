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
        $this->setName('veneer:core:workspace:git-hook:update')
            ->setDescription('A server-side hook for git repository updates.')
            ->addArgument('ref', InputArgument::REQUIRED, 'The name of the reference (branch).')
            ->addArgument('old-commit', InputArgument::REQUIRED, 'The SHA-1 the reference pointed to before the push.')
            ->addArgument('new-commit', InputArgument::REQUIRED, 'The SHA-1 the user is trying to push.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}