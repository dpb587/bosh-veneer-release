<?php

namespace Veneer\WellnessBundle\Service\Check;

class Check implements \ArrayAccess
{
    const STATE_OK = 'ok';
    const STATE_ALARM = 'alarm';
    const STATE_ERROR = 'error';

    protected $context;
    protected $sourceConfig;
    protected $source;

    public function setContextValue($key, $value)
    {
        $this->context[$key] = $value;

        return $this;
    }

    public function mergeContext(array $context)
    {
        $this->context = array_merge($this->context, $context);

        return $this;
    }

    public function setSourceConfig(array $config)
    {
        $this->sourceConfig = $config;

        return $this;
    }

    public function setSource(array $source)
    {
        $this->source = $source;

        return $this;
    }

    public function offsetGet($offset)
    {
        switch ($offset) {
            case 'context':
                return $this->context;
            case 'source':
                return $this->source;
            case '_source':
                return $this->sourceConfig;
        }

        $exp = explode('.', $offset, 2);

        switch ($exp[0]) {
            case 'context':
                return $this->context[$exp[1]];
            case 'source':
                return $this->source[$exp[1]];
            case '_source':
                return $this->sourceConfig[$exp[1]];
            default:
                return;
        }
    }
    
    public function offsetExists($offset)
    {
        return null !== $this[$offset];
    }

    public function offsetSet($offfset, $value)
    {
        throw new \BadMethodCallException('Array access to Check is read only');
    }

    public function offsetUnset($offfset)
    {
        throw new \BadMethodCallException('Array access to Check is read only');
    }
}
