<?php

namespace Veneer\WellnessBundle\Service\Check\Action;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Veneer\WellnessBundle\Service\Check\Check;

class EmailAction implements ActionInterface
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getConfiguration(NodeDefinition $tree)
    {
        $tree->children()
            ->scalarNode('mailer')
                ->info('Service name for mailer')
                ->defaultValue('mailer')
                ->end()
            ->scalarNode('from')
                ->info('The sender of the message')
                ->isRequired()
                ->end()
            ->arrayNode('to')
                ->info('The recipients of the message')
                ->prototype('string')
                    ->end()
                ->isRequired()
                ->end()
            ->scalarNode('reply_to')
                ->info('The Reply-To of the message')
                ->isRequired()
                ->end()
            ->scalarNode('priority')
                ->info('The priority of the message (lowest, low, normal, high, highest')
                ->defaultValue('normal')
                ->end()
            ->scalarNode('subject')
                ->info('The subject of the message')
                ->isRequired()
                ->end()
            ->scalarNode('body')
                ->info('The body of the message')
                ->isRequired()
                ->end()
            ;
    }

    public function execute(Check $check)
    {
        $mailer = $this->container->get($check['_action.mailer']);

        
    }
}
