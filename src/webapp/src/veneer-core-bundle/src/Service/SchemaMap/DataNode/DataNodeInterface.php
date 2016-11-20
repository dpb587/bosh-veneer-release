<?php

namespace Veneer\CoreBundle\Service\SchemaMap\DataNode;

interface DataNodeInterface
{
    /**
     * @return DataNodeInterface|TraversableDataNodeInterface
     */
    public function getRoot();

    /**
     * @return DataNodeInterface|TraversableDataNodeInterface
     */
    public function getParent();

    /**
     * @return string
     */
    public function getRelativePath();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return mixed
     */
    public function getData();

    /**
     * @return boolean
     */
    public function hasData();

    /**
     * @return self
     */
    public function setData($data);

    /**
     * @return self
     */
    public function unsetData();

    /**
     * @return mixed
     */
    public function applyData($data);
}
