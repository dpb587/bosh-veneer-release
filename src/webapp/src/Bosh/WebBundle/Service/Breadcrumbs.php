<?php

namespace Bosh\WebBundle\Service;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Breadcrumbs implements ArrayAccess, Countable, IteratorAggregate
{
    protected $compiled = false;
    protected $router;
    protected $trail = [];

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function add($title, $primary = null)
    {
        $this->compiled = false;
        $this->trail[] = array(
            'title' => $title,
            'primary' => $primary,
        );

        return $this;
    }

    protected function compile()
    {
        if (true === $this->compiled) {
            return;
        }

        foreach ($this->trail as &$crumb) {
            if (isset($crumb['url'])) {
                continue;
            }

            if (!isset($crumb['primary'])) {
                $crumb['url'] = null;
            } elseif (is_string($crumb['primary'])) {
                $crumb['url'] = $crumb['primary'];
            } else {
                $crumb['url'] = $this->router->generate($crumb['primary'][0], isset($crumb['primary'][1]) ? $crumb['primary'][1] : []);
            }
        }

        $this->compiled = true;
    }

    public function count()
    {
        return count($this->trail);
    }

    public function getIterator()
    {
        $this->compile();

        return new ArrayIterator($this->trail);
    }

    public function offsetExists($offset)
    {
        return isset($this->trail[($offset < 0) ? (count($this->trail) + $offset) : $offset]);
    }

    public function offsetGet($offset)
    {
        $this->compile();

        return $this->trail[($offset < 0) ? (count($this->trail) + $offset) : $offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('Breadcrumb trail via ArrayAccess is read-only.');
    }

    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('Breadcrumb trail via ArrayAccess is read-only.');
    }
}