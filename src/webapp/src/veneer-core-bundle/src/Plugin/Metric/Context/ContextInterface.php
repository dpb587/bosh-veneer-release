<?php

namespace Veneer\CoreBundle\Plugin\Metric\Context;

interface ContextInterface
{
    public function resolve($name);
    public function replaceContext($context);
    public function addContext($key, $value);
    public function getContext($key);
}
