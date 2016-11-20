<?php

namespace Veneer\CoreBundle\Service\SchemaEditor\CloudConfig;

abstract class AbstractNode
{
    protected $parentNode;
    protected $data = [];
    protected $prototypeNodes = [];
    protected $childrenNodes = [];

    public function __construct(AbstractNode $parentNode, array $data)
    {
        $this->parentNode = $parentNode;
        $this->data = $data;
    }

    protected function register($key, \Closure $builder)
    {
        $this->prototypeNodes[$key] = $builder;
    }

    public function traverse($path)
    {
        $slugs = explode('/', $path);
        $slug = array_shift($slugs);

        if (null === $slug) {
            return $this;
        } elseif (isset($this->childrenNodes[$slug])) {
            // good
        } elseif (isset($this->prototypeNodes[$slug])) {
            $this->childrenNodes[$slug] = call_user_func(
                $this->prototypeNodes[$slug],
                $this,
                isset($this->data[$slug]) ? null : $this->data[$slug]
            );
        } else {
            $this->childrenNodes[$slug] = new ArbitraryNode($this, isset($this->data[$slug]) ? null : $this->data[$slug]);
        }

        return $this->childrenNodes[$slug]->traverse(implode('/', $slugs));
    }

    public function getData()
    {
        $data = $this->data;

        foreach ($this->childrenNodes as $key => $childNode) {
            $data[$key] = $childNode->getData();
        }

        return $data;
    }
}