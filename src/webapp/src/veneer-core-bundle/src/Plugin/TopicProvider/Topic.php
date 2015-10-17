<?php

namespace Veneer\CoreBundle\Plugin\TopicProvider;

class Topic
{
    const PRIORITY_DISABLED = -481516;

    protected $priority;
    protected $name;
    protected $title;
    protected $route;
    protected $url;

    public function __construct($name, $title = null)
    {
        $this->name = $name;

        if (null !== $title) {
            $this->setTitle($title);
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    public function getPriority()
    {
        return (int) $this->priority;
    }

    public function setRoute($name, array $params = [])
    {
        $this->route = [
            $name,
            $params,
        ];

        return $this;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }
}
