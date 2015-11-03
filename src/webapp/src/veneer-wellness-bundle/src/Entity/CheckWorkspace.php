<?php

namespace Veneer\WellnessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="wellness_check_workspace")
 */
class CheckWorkspace
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
     * @ORM\Column(name="has_readme", type="boolean")
     */
    protected $hasReadme;

    public function setHasReadme($hasReadme)
    {
        $this->hasReadme = $hasReadme;

        return $this;
    }

    public function getHasReadme()
    {
        return $this->hasReadme;
    }
}
