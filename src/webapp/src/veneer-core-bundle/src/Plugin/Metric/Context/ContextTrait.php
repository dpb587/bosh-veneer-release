<?php

namespace Veneer\CoreBundle\Plugin\Metric\Context;

use Veneer\CoreBundle\Plugin\RequestContext\Context;

trait ContextTrait
{
    protected $context = [];

    public function addContext($key, $value)
    {
        $this->context[$key] = $value;
    }

    public function replaceContext($context)
    {
        $this->context = new Context();

        foreach ($context as $k => $v) {
            $this->context[$k] = $v;
        }
    }

    public function getContext($key = null)
    {
        if (null === $key) {
            return $this->context;
        }

        return $this->context[$key];
    }
}
