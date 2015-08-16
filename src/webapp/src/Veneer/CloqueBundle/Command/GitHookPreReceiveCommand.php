<?php

namespace Veneer\CloqueBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Process\Process;

class GitHookPreReceiveCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('old', InputArgument::REQUIRED, 'Old Commit'),
                new InputArgument('new', InputArgument::REQUIRED, 'New Commit'),
                new InputArgument('ref', InputArgument::REQUIRED, 'Reference Name'),
            ))
            ->setName('bosh:cloque:git-hook:pre-receive')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $compileDir = sprintf(
            '%s/compiled/%s/',
            $this->getContainer()->getParameter('veneer_cloque.versioning.repository.path_prefix'),
            $this->getContainer()->getParameter('veneer_cloque.director_name')
        );

        $p = new Process(
            sprintf(
                '%s --git-dir=%s diff --name-only %s..%s -- %s',
                $this->getContainer()->getParameter('veneer_cloque.executable.git'),
                escapeshellarg($this->getContainer()->getParameter('veneer_cloque.versioning.repository.workspace')),
                $input->getArgument('old'),
                $input->getArgument('new'),
                escapeshellarg($compileDir)
            )
        );

        $p->mustRun();

        $task = rand(4013, 4192);
        
        foreach (explode("\n", trim($p->getOutput())) as $file) {
            $relpath = preg_replace('#^' . preg_quote($compileDir) . '(.+)$#', '$1', $file);

            if (!preg_match('#^([^/]+)/bosh(-([^\.]+))?\.yml$#', $relpath, $match)) {
                continue;
            }

            $name = $match[1] . (isset($match[3]) ? ('-' . $match[3]) : '');

            $task += 1;

            $output->writeln('<comment>' . $name . '</comment> updated');
            $output->writeln('  [cli] <info>bosh task ' . $task . '</info>');
            $output->writeln('  [web] <info>https://10.163.16.123/bosh/task/' . $task . '/summary</info>');
        }
    }
}
