<?php

namespace Veneer\CoreBundle\Service\Workspace\Checkout;

interface CheckoutInterface
{
    const MODE_WRITABLE = 1;
    const MODE_DESTROYABLE = 2;
    const MODE_DESTRUCT_DESTROY = 4;

    public function cd($path);
    public function ls($path);
    public function get($path);
    public function put($path, $data, $mode = 0600);
    public function delete($path);
}
