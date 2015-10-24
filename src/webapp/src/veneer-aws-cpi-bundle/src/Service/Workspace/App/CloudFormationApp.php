<?php

namespace Veneer\AwsCpiBundle\Service\Workspace\App;

use Veneer\CoreBundle\Service\Workspace\App\AppInterface;

class CloudFormationApp implements AppInterface
{
    public function getAppTitle()
    {
        return 'CloudFormation Editor';
    }

    public function getAppDescription()
    {
        return 'Edit and apply CloudFormation templates';
    }

    public function getAppRoute()
    {
        return 'veneer_awscpi_workspace_app_cloudformation_summary';
    }
}
