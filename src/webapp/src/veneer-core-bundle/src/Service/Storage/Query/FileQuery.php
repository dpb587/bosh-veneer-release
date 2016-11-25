<?php

namespace Veneer\CoreBundle\Service\Storage\Query;

use Veneer\CoreBundle\Service\Storage\Object\File;

class FileQuery extends AbstractQuery
{
    protected $file;

    public function setFile(File $file)
    {
        $this->file = $file;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    protected function getResult()
    {
        return $this->getFile();
    }
}
