<?php

namespace Veneer\BoshBundle\Controller\Plugin\WebTopicProvider;

use Symfony\Component\HttpFoundation\Request;
use Veneer\WebBundle\Plugin\TopicProvider\PluginInterface;
use Veneer\WebBundle\Plugin\TopicProvider\Topic;

class ReleasePlugin implements PluginInterface
{
    public function getTopics(Request $request, $context)
    {
        $_bosh = $request->attributes->get('_bosh');

        return [
            (new Topic('bosh_bosh'))
                ->setTitle('bosh')
                ->setRoute(
                    'veneer_bosh_release_summary',
                    [
                        'release' => $_bosh['release']['name'],
                    ]
                ),
        ];
    }
}
