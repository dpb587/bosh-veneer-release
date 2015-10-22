<?php

namespace Veneer\CoreBundle\Service\Workspace\Environment
;

class EnvironmentContext implements \ArrayAccess
{
    protected $factory;
    protected $path;
    protected $caller;
    protected $aliases;

    protected $cache = [];

    public function __construct(EnvironmentFactory $factory, $path, $caller, array $aliases = [])
    {
        $this->factory = $factory;
        $this->path = $path;
        $this->caller = $caller;
        $this->aliases = $aliases;
    }

    public function getContextPath()
    {
        return $this->path;
    }

    public function getContextCaller()
    {
        return $this->caller;
    }

    public function offsetGet($offset)
    {
        if (!isset($this->cache[$offset])) {
            $sp = explode(':', $offset, 2);

            $this->cache[$offset] = $this->factory->get($sp[0])->loadEnvironment($this, $sp[1]);
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
}
