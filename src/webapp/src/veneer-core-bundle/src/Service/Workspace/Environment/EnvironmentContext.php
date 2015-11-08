<?php

namespace Veneer\CoreBundle\Service\Workspace\Environment;

use Veneer\CoreBundle\Service\Workspace\Checkout\CheckoutInterface;

class EnvironmentContext implements \ArrayAccess
{
    protected $checkout;
    protected $factory;
    protected $path;
    protected $caller;

    protected $cache = [];

    public function __construct(CheckoutInterface $checkout, EnvironmentFactory $factory, $path, $caller)
    {
        $this->checkout = $checkout;
        $this->factory = $factory;
        $this->path = $path;
        $this->caller = $caller;
    }

    public function getContextPath()
    {
        return $this->path;
    }

    public function getContextCaller()
    {
        return $this->caller;
    }

    public function getCheckout()
    {
        return $this->checkout;
    }

    public function offsetGet($offset)
    {
        if (!isset($this->cache[$offset])) {
            $sp = explode(':', $offset, 2);

            $this->cache[$offset] = $this->factory->get($sp[0])->load($this, isset($sp[1]) ? $sp[1] : null);
        }

        return $this->cache[$offset];
    }
    
    public function offsetExists($offset)
    {
        try {
            return $this->offsetGet($offset);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('Environment lookup is read-only');
    }

    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('Environment lookup is read-only');
    }

    public function resolveRelativePath($path)
    {
        return dirname($this->path) . '/' . $path;
    }
}
