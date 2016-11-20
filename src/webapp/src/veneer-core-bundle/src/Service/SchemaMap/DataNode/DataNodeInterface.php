<?php

namespace Veneer\CoreBundle\Service\SchemaMap\DataNode;

interface DataNodeInterface
{
    public function getRoot();
    public function getParent();
    public function getRelativePath();
    public function getPath();

    public function getData();
    public function hasData();
    public function setData($data);
    public function unsetData();
    public function applyData($data);
}
