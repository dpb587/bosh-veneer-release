<?php

namespace Veneer\CloqueBundle\Controller\Plugin\WebTopicProvider;

use Symfony\Component\HttpFoundation\Request;
use Veneer\WebBundle\Plugin\TopicProvider\PluginInterface;
use Veneer\WebBundle\Plugin\TopicProvider\Topic;

class DeploymentPlugin implements PluginInterface
{
    public function getTopics(Request $request, $context)
    {
        $_bosh = $request->attributes->get('_bosh');

        return [
            (new Topic('cloque_infra'))
                ->setTitle('infra')
                ->setRoute(
                    'veneer_cloque_deployment_infra_index',
                    [
                        'deployment' => $_bosh['deployment']['name'],
                    ]
                ),
        ];
    }
}
