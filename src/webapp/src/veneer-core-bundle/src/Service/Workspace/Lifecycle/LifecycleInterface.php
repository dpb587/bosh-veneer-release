<?php

namespace Veneer\CoreBundle\Service\Workspace\Lifecycle;

use Psr\Log\LoggerInterface;
use Veneer\CoreBundle\Service\Workspace\Checkout\CheckoutInterface;

interface LifecycleInterface
{
    /**
     * Compile the configuration and write it to the file system.
     *
     * @param CheckoutInterface $checkout
     * @param string            $path
     */
    public function onCompile(CheckoutInterface $checkout, $path);

    /**
     * Compare an existing commit to a new one and return a plan if changes need to be applied.
     *
     * @param CheckoutInterface $existing
     * @param CheckoutInterface $target
     * @param string            $path
     */
    public function onPlan(CheckoutInterface $existing, CheckoutInterface $target, $path, array $compiled);

    /**
     * A plan suggested changes need to be made; apply whatever changes should happen.
     *
     * @param CheckoutInterface $checkout
     * @param string            $path
     */
    public function onApply(LoggerInterface $logger, array $compiled);
}
