<?php

namespace Veneer\CoreBundle\Command;

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
use Veneer\CoreBundle\Service\Workspace\Environment\EnvironmentContext;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Yaml\Yaml;

class WorkspaceInitializeGitRepositoryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('veneer:core:workspace:initialize-git-repository')
            ->setDescription('Initialize a git repository workspace')
            ->addOption('reset', null, InputOption::VALUE_NONE, 'Reset an existing workspace')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $container = $this->getContainer();
        $env = new EnvironmentContext(
            $container->get('veneer_core.workspace.repository')->createCheckout($input->getOption('ref')),
            $container->get('veneer_core.workspace.environment'),
            $input->getArgument('file'),
            'cli'
        );

        $context = $env[$input->getArgument('context')];

        if ($input->getArgument('key')) {
            $accessor = PropertyAccess::createPropertyAccessor();

            $key = '[' . implode('][', explode('.', $input->getArgument('key'))) . ']';
            $value = $accessor->getValue($context, $key);
        } else {
            $value = $context;
        }

        switch ($input->getOption('format')) {
            case 'json':
                $formatted = json_encode($value, JSON_PRETTY_PRINT);

                break;
            case 'yaml':
                $formatted = Yaml::dump($value, 4);

                break;
            case 'php':
                $formatted = serialize($value);

                break;
            case 'plain':
                $formatted = $value;

                break;
            default:
                throw new \InvalidArgumentException('Format is not supported');
        }

        $output->writeln($formatted, OutputInterface::OUTPUT_RAW);
    }
}
