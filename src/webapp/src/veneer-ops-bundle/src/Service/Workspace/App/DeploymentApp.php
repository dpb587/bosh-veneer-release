<?php

namespace Veneer\OpsBundle\Service\Workspace\App;

use Veneer\CoreBundle\Service\Workspace\App\AppInterface;

class DeploymentApp implements AppInterface
{
    public function getAppTitle()
    {
        return 'Deployment Editor';
    }

    public function getAppDescription()
    {
        return 'Edit the various aspects of your deployment manifests';
    }

    public function getAppRoute()
    {
        return 'veneer_ops_workspace_app_deployment_summary';
    }
}
