<?php

namespace Veneer\WellnessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class CheckState
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
     * @ORM\Column(name="source", type="array", nullable=true)
     */
    protected $source;
    
    public function setSource($source)
    {
        $this->source = $source;
        
        return $this;
    }
    
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @ORM\Column(name="state", type="string", length=8)
     */
    protected $state;

    public function setState($state)
    {
        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }
}
