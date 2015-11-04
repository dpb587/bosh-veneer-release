<?php

namespace Veneer\CoreBundle\Plugin\Metric;

class Resolver
{
    protected $root;

    public function __construct(Context\ContextInterface $root)
    {
        $this->root = $root;
    }

    public function resolve($name)
    {
        $names = explode('.', $name);

        $context = $this->root;

        foreach ($names as $name) {
            $context = $context->resolve($name);
        }

        return $context;
    }
}
