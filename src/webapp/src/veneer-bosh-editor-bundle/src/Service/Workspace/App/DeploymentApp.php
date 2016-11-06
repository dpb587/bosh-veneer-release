<?php

namespace Veneer\BoshEditorBundle\Service\Workspace\App;

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
        return 'veneer_bosh_editor_app_deployment_summary';
    }
}
