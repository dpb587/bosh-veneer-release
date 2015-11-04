<?php

namespace Veneer\CoreBundle\Plugin\Metric\Context;

trait ContextTrait
{
    protected $context = [];

    public function addContext($key, $value)
    {
        $this->context[$key] = $value;
    }

    public function replaceContext(array $context)
    {
        $this->context = $context;
    }

    public function getContext($key = null)
    {
        if (null === $key) {
            return $this->context;
        }

        return $this->context[$key];
    }
}
