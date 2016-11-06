<?php

namespace Veneer\CoreBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Veneer\CoreBundle\Service\Workspace\Checkout\CheckoutInterface;
use Veneer\CoreBundle\Service\Workspace\Checkout\BufferedWriteCheckout;

class WorkspaceLifecycleCompileCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('veneer-core:workspace:lifecycle:compile')
            ->setDescription('Read environment')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not commit compilation changes')
            ->addOption('write-to', null, InputOption::VALUE_REQUIRED, 'Commit changes to a separate branch')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Overwrite branch when committing to a separate branch')
            ->addOption('ref', null, InputOption::VALUE_REQUIRED, 'Repository reference', 'master')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $repository = $container->get('veneer_core.workspace.repository');

        $checkout = $repository->createCheckout(
            $input->getOption('ref'),
            !$input->getOption('dry-run') ? CheckoutInterface::MODE_WRITABLE : 0
        );

        if ($input->getOption('dry-run')) {
            $checkout = new BufferedWriteCheckout($checkout);
        }

        $container->get('veneer_core.workspace.lifecycle')->compile($checkout);

        if ($input->getOption('dry-run')) {
            foreach ($checkout->getWrites() as $path => $write) {
                $output->writeln('<info>'.$path.'</info> ('.sprintf('%4o', $write['mode']).')');
                $output->writeln('    '.str_replace("\n", "\n    ", $write['data']));
            }

            return;
        }

        $repository->commitCheckout(
            $checkout,
            'bosh-veneer compiled',
            [
                'author' => [
                    'name' => getenv('GIT_AUTHOR_NAME'),
                    'email' => getenv('GIT_AUTHOR_EMAIL'),
                ],
                'branch' => $input->getOption('write-to') ?: $input->getOption('ref'),
                'force' => $input->getOption('force'),
            ]
        );
    }
}
