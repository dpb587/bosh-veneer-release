<?php

namespace Veneer\CoreBundle\Plugin\TopicProvider;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class Factory
{
    protected $container;
    protected $contextMap;

    public function __construct(ContainerInterface $container, array $contextMap = [])
    {
        $this->container = $container;
        $this->contextMap = $contextMap;
    }

    public function getTopics(Request $request, $context)
    {
        $topics = [];

        if (!isset($this->contextMap[$context])) {
            return $topics;
        }

        foreach ($this->contextMap[$context] as $service) {
            foreach ($this->container->get($service)->getTopics($request, $context) as $topic) {
                $topics[$topic->getName()][] = $topic;
            }
        }

        foreach ($topics as &$topic) {
            uasort(
                $topic,
                function ($a, $b) {
                    $ap = $a->getPriority();
                    $bp = $b->getPriority();

                    if ($ap == $bp) {
                        return 0;
                    }

                    return ($ap < $bp) ? -1 : 1;
                }
            );
        }

        $router = $this->container->get('router');

        return array_map(
            function ($link) use ($router) {
                if (!$link->getUrl()) {
                    $link->setUrl($router->generate($link->getRoute()[0], $link->getRoute()[1]));
                }

                return $link;
            },
            array_filter(
                array_map(
                    function (array $topics) {
                        return $topics[0];
                    },
                    $topics
                ),
                function ($topic) {
                    return Topic::PRIORITY_DISABLED != $topic->getPriority();
                }
            )
        );
    }
}
