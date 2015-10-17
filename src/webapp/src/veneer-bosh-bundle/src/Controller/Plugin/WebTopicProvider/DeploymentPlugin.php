<?php

namespace Veneer\BoshBundle\Controller\Plugin\WebTopicProvider;

use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Plugin\TopicProvider\PluginInterface;
use Veneer\CoreBundle\Plugin\TopicProvider\Topic;

class DeploymentPlugin implements PluginInterface
{
    public function getTopics(Request $request, $context)
    {
        $_bosh = $request->attributes->get('_bosh');

        return [
            (new Topic('bosh_bosh'))
                ->setTitle('bosh')
                ->setRoute(
                    'veneer_bosh_deployment_summary',
                    [
                        'deployment' => $_bosh['deployment']['name'],
                    ]
                ),
        ];
    }
}
