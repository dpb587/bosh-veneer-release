<?php

namespace Veneer\CoreBundle\Service\Storage\Layer;

use Veneer\CoreBundle\Service\Storage\Query\DirectoryQuery;
use Veneer\CoreBundle\Service\Storage\Query\FileQuery;

interface LayerInterface
{
    public function get(FileQuery $query);
    public function put(FileQuery $query);
    public function ls(DirectoryQuery $query);
    public function rm(FileQuery $query);
}
