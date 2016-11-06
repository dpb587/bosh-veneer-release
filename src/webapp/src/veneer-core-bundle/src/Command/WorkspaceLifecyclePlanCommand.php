<?php

namespace Veneer\CoreBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class WorkspaceLifecyclePlanCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('veneer-core:workspace:lifecycle:plan')
            ->setDescription('Plan changes when moving from one commit to another')
            ->addArgument('base-ref', InputArgument::REQUIRED, 'Repository base reference')
            ->addArgument('changed-ref', InputArgument::REQUIRED, 'Repository changed reference')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $repository = $container->get('veneer_core.workspace.repository');

        $baseCheckout = $repository->createCheckout($input->getArgument('base-ref'));
        $changedCheckout = $repository->createCheckout($input->getArgument('changed-ref'));

        $container->get('veneer_core.workspace.lifecycle')->compile($baseCheckout, $changedCheckout);
    }
}
