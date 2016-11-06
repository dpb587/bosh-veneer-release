<?php

namespace Veneer\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerMap implements \ArrayAccess
{
    private $container;
    private $map;

    public function __construct(ContainerInterface $container, array $map)
    {
        $this->container = $container;
        $this->map = $map;
    }

    public function all()
    {
        $all = array();

        foreach ($this->map as $k => $v) {
            $all[$k] = $this->get($k);
        }

        return $all;
    }

    public function allKeys()
    {
        return array_keys($this->map);
    }

    public function mapKeys($callback)
    {
        $i = 0;
        $all = array();

        foreach ($this->map as $k => $v) {
            $all[$k] = $callback($this->get($k), $k, $i);

            if (null === $all[$k]) {
                unset($all[$k]);
            }

            $i += 1;
        }

        return $all;
    }

    public function get($name)
    {
        if (!isset($this->map[$name])) {
            throw new \InvalidArgumentException(sprintf('The "%s" service is not defined in the map [%s].', $name, implode(',', array_keys($this->map))));
        }

        return $this->container->get($this->map[$name]);
    }

    public function has($name)
    {
        return isset($this->map[$name]);
    }

    /**
     * ArrayAccess.
     */
    public function offsetExists($offset)
    {
        return isset($this->map[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException();
    }

    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException();
    }
}
