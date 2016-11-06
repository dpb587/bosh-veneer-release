<?php

namespace Veneer\CoreBundle\Service\Workspace;

interface RepositoryInterface
{
    public function createCheckout($ref = 'master', $mode = 0);
    public function commitCheckout(Checkout\PhysicalCheckout $checkout, $message, array $options = []);
    public function listDirectory($directory = '.', $ref = 'HEAD');
    public function showFile($file, $ref = 'HEAD');
    public function getPrefixedPath($path);
    public function diff($oldRef, $newRef);
    public function commitWrites($profile, array $writes, $message = null);
    public function getDraftProfile($draft, $path);
}
