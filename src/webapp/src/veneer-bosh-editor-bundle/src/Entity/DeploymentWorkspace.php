<?php

namespace Veneer\BoshEditorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ops_deployment_workspace")
 */
class DeploymentWorkspace
{
    /**
     * @ORM\Id
     * @ORM\Column(name="deployment", type="string", length=128)
     */
    protected $deployment;

    public function setDeployment($deployment)
    {
        $this->deployment = $deployment;

        return $this;
    }

    public function getDeployment()
    {
        return $this->deployment;
    }

    /**
     * @ORM\Column(name="source_path", type="string", length=255)
     */
    protected $sourcePath;

    public function setSourcePath($sourcePath)
    {
        $this->sourcePath = $sourcePath;

        return $this;
    }

    public function getSourcePath()
    {
        return $this->sourcePath;
    }
}
