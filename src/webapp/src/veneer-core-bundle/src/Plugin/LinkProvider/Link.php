<?php

namespace Veneer\CoreBundle\Plugin\LinkProvider;

class Link
{
    const TOPIC_CONFIG = 'config';
    const TOPIC_RESOURCES = 'resources';
    const TOPIC_PERFORMANCE = 'performance';
    const TOPIC_CPI = 'cpi';
    const TOPIC_DOCUMENTATION = 'documentation';
    const TOPIC_OTHER = 'other';
    const TOPIC_WIDGET = 'widget';

    const PRIORITY_DISABLED = -481516;

    protected $priority;
    protected $name;
    protected $topic = self::TOPIC_OTHER;
    protected $title;
    protected $note;
    protected $route;
    protected $url;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setTopic($topic)
    {
        $this->topic = $topic;

        return $this;
    }

    public function getTopic()
    {
        return $this->topic;
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

    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    public function getNote()
    {
        return $this->note;
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
