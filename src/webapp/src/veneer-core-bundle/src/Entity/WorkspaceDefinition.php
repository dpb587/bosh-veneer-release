<?php

namespace Veneer\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * !ORM\Entity
 * !ORM\Table(name="core_workspace_definition")
 */
class WorkspaceDefinition
{
    /**
     * @ORM\Column(name="path", type="string", length=128)
     */
    protected $path;

    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }

    /**
     * @ORM\Column(name="definition_type", type="string", length=32)
     */
    protected $definitionType;

    public function setDefinitionType($definitionType)
    {
        $this->definitionType = $definitionType;

        return $this;
    }

    public function getDefinitionType()
    {
        return $this->definitionType;
    }

    /**
     * @ORM\Column(name="definition_name", type="string", length=128)
     */
    protected $definitionName;

    public function setDefinitionName($definitionName)
    {
        $this->definitionName = $definitionName;

        return $this;
    }

    public function getDefinitionName()
    {
        return $this->definitionName;
    }

    /**
     * @ORM\Column(name="definition_data", type="array", nullable=true)
     */
    protected $definitionData;

    public function setDefinitionData(array $definitionData = null)
    {
        $this->definitionData = $definitionData;

        return $this;
    }

    public function getDefinitionData()
    {
        return $this->definitionData;
    }
}
