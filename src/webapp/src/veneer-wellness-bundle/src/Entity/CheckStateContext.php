<?php

namespace Veneer\WellnessBundle\Entity;

class CheckStateContext
{
    /**
     * @ORM\Column(name="workspace_path", type="string", length=255)
     */
    protected $workspacePath;

    public function setWorkspacePath($workspacePath)
    {
        $this->workspacePath = $workspacePath;

        return $this;
    }

    public function getWorkspacePath()
    {
        return $this->workspacePath;
    }

    /**
     * @ORM\Column(name="context_ref", type="string", length=32)
     */
    protected $contextRef;

    public function setContextRef($contextRef)
    {
        $this->contextRef = $contextRef;

        return $this;
    }

    public function getContextRef()
    {
        return $this->contextRef;
    }

    /**
     * @ORM\Column(name="context_key", type="string", length=64)
     */
    protected $contextKey;

    public function setContextKey($contextKey)
    {
        $this->contextKey = $contextKey;

        return $this;
    }

    public function getContextKey()
    {
        return $this->contextKey;
    }

    /**
     * @ORM\Column(name="context_value", type="string", length=128)
     */
    protected $contextValue;

    public function setContextValue($contextValue)
    {
        $this->contextValue = $contextValue;

        return $this;
    }

    public function getContextValue()
    {
        return $this->contextValue;
    }
}
