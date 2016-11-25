<?php

namespace Veneer\CoreBundle\Service\Storage\Object;

class File extends AbstractObject
{
    protected $data;

    public function getObjectType()
    {
        return static::OBJECT_TYPE_FILE;
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }
}
