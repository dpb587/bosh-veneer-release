<?php

namespace Veneer\OpsBundle\Service\Workspace;

use Veneer\CoreBundle\Service\Workspace\EditorInterface;

class DeploymentEditor implements EditorInterface
{
    public function getTitle()
    {
        return 'Deployment Editor';
    }

    public function getDescription()
    {
        return 'Edit the various aspects of your deployment manifests';
    }

    public function getRoute()
    {
        return 'veneer_ops_workspace_deploymenteditor_summary';
    }
}
