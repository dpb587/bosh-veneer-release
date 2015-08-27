<?php

namespace Veneer\WebBundle\Plugin\Bundle;

use Symfony\Component\HttpKernel\KernelInterface;

class Factory
{
    protected $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function getBundles()
    {
        return array_filter(
            $this->kernel->getBundles(),
            function ($bundle) {
                return $bundle instanceof BundleInterface;
            }
        );
    }
}
