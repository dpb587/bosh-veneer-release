<?php

namespace Veneer\CoreBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

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
        if (0 === strpos($input->getArgument('ref'), 'refs/heads/veneer-draft-')) {
            return;
        }

        $container = $this->getContainer();
        $repository = $container->get('veneer_core.workspace.repository');
        $watcher = $container->get('veneer_core.workspace.watcher');

        $changeset = $repository->diff($input->getArgument('old-commit'), $input->getArgument('new-commit'));
        $watcher->handleChangeset($input->getArgument('ref'), $changeset);
    }
}
