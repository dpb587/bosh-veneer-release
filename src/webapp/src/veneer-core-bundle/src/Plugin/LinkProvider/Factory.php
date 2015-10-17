<?php

namespace Veneer\CoreBundle\Plugin\LinkProvider;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class Factory
{
    protected $container;
    protected $routeMap;

    public function __construct(ContainerInterface $container, array $routeMap = [])
    {
        $this->container = $container;
        $this->routeMap = $routeMap;
    }

    public function getLinks(Request $request, $route)
    {
        $links = [];

        if (!isset($this->routeMap[$route])) {
            return $links;
        }

        foreach ($this->routeMap[$route] as $service) {
            foreach ($this->container->get($service)->getLinks($request, $route) as $link) {
                $links[$link->getName()][] = $link;
            }
        }

        foreach ($links as &$link) {
            uasort(
                $link,
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

        $validLinks = array_map(
            function ($link) use ($router) {
                if (!$link->getUrl()) {
                    $link->setUrl($router->generate($link->getRoute()[0], $link->getRoute()[1]));
                }

                return $link;
            },
            array_filter(
                array_map(
                    function (array $links) {
                        return current($links);
                    },
                    $links
                ),
                function ($link) {
                    return Link::PRIORITY_DISABLED != $link->getPriority();
                }
            )
        );

        $topicLinks = [];

        foreach ($validLinks as $linkName => $link) {
            $topicLinks[$link->getTopic()][$linkName] = $link;
        }

        return $topicLinks;
    }
}
