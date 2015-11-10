<?php

namespace Veneer\OpsBundle\Service\Workspace\App;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Veneer\CoreBundle\Service\Workspace\App\AppInterface;
use Veneer\CoreBundle\Service\Workspace\Changeset;
use Veneer\CoreBundle\Service\Workspace\GitRepository;
use Symfony\Component\Yaml\Yaml;
use Veneer\OpsBundle\Entity\DeploymentWorkspace;
use Psr\Log\LoggerInterface;
use Veneer\CoreBundle\Service\Workspace\Checkout\CheckoutInterface;
use Veneer\CoreBundle\Service\Workspace\Environment\EnvironmentContext;

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
