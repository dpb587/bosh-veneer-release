<?php

namespace Veneer\SheafBundle\Service\Workspace\App;

use Symfony\Component\HttpFoundation\Request;
use Veneer\BoshBundle\Service\DirectorApiClient;
use Veneer\CoreBundle\Plugin\LinkProvider\Link;
use Veneer\CoreBundle\Plugin\LinkProvider\PluginInterface;
use Veneer\CoreBundle\Service\ManifestDiff;
use Veneer\CoreBundle\Service\Workspace\App\AppInterface;
use Symfony\Component\Yaml\Yaml;
use Veneer\CoreBundle\Service\Workspace\Lifecycle\LifecycleInterface;
use Psr\Log\LoggerInterface;
use Veneer\CoreBundle\Service\Workspace\Checkout\CheckoutInterface;
use Veneer\BoshEditorBundle\Service\ManifestBuilder\ManifestBuilderInterface;

class SheafApp implements AppInterface
{
    public function getAppTitle()
    {
        return 'Sheaf Editor';
    }

    public function getAppDescription()
    {
        return 'Edit the various aspects of your cloud config';
    }

    public function getAppRoute()
    {
        return 'veneer_sheaf_app_summary';
    }
}
