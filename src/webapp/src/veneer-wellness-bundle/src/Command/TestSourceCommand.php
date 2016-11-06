<?php

namespace Veneer\WellnessBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Veneer\WellnessBundle\Service\Check\Check;

class TestSourceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('veneer:wellness:test-source')
            ->setDescription('Test a source')
            ->addArgument('file', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Test source file.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $sourceFactory = $container->get('veneer_wellness.check.source');

        foreach ($input->getArgument('file') as $file) {
            $config = json_decode(file_get_contents($file), true);

            $sourceName = key($config['source']);
            $sourceConfig = $sourceFactory->compileConfig($sourceName, $config['source'][$sourceName]);

            $check = new Check();
            $check->setContextValue('source_path', $file);
            $check->setSourceConfig($sourceConfig);

            foreach ($sourceFactory->get($sourceName)->load($check) as $check) {
                $output->writeln('<info>'.$check['context.source_path'].'</info>');

                foreach ($check['context'] as $key => $value) {
                    if (in_array($key, ['source_path'])) {
                        continue;
                    }

                    $output->writeln('  with <comment>'.$key.'</comment>: '.$value);
                }

                foreach ($check['source'] as $key => $value) {
                    $output->writeln('  <comment>'.$key.'</comment>: '.$value);
                }
            }
        }
    }
}
