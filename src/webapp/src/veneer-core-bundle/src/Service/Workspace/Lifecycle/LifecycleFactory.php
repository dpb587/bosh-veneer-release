<?php

namespace Veneer\CoreBundle\Service\Workspace\Lifecycle;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Veneer\CoreBundle\Service\Workspace\Checkout\CheckoutInterface;

class LifecycleFactory
{
    protected $container;
    protected $map;

    public function __construct(ContainerInterface $container, array $map)
    {
        $this->container = $container;
        $this->map = $map;
    }

    public function findHandler($path)
    {
        foreach ($this->map as $handler) {
            if (preg_match($handler['path'], $path)) {
                return $this->container->get($handler['service']);
            }
        }

        return null;
    }

    public function compile(CheckoutInterface $checkout, $path = '.')
    {
        foreach ($checkout->ls($path) as $item) {
            $itempath = ltrim($path.'/'.$item['name'], '/');

            if ('dir' == $item['type']) {
                $this->compile($checkout, $itempath);
            } elseif ('file' == $item['type']) {
                $handler = $this->findHandler($itempath);

                if (null !== $handler) {
                    $handler->onCompile($checkout, $itempath);
                }
            }
        }
    }
}
