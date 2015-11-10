<?php

namespace Veneer\CoreBundle\Service\Workspace\Checkout;

class BufferedWriteCheckout implements CheckoutInterface
{
    protected $checkout;
    protected $writes = [];

    public function __construct(CheckoutInterface $checkout)
    {
        $this->checkout = $checkout;
    }

    public function getHead()
    {
        return $this->checkout->getHead();
    }

    public function getWrites()
    {
        return $this->writes;
    }

    public function cd($path)
    {
        return $this->checkout->cd($path);
    }

    public function ls($path)
    {
        return $this->checkout->ls($path);
    }

    public function get($path)
    {
        return $this->checkout->get($path);
    }

    public function put($path, $data, $mode = 0600)
    {
        if ($this->get($path) != $data) {
            $this->writes[$path] = [
                'data' => $data,
                'mode' => $mode,
            ];
        }

        return $this;
    }

    public function delete($path)
    {
        $this->writes[$path] = null;

        return $this;
    }
}
