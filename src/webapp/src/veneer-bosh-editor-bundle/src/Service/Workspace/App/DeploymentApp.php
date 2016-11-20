<?php

namespace Veneer\BoshEditorBundle\Service\Workspace\App;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Plugin\LinkProvider\Link;
use Veneer\CoreBundle\Plugin\LinkProvider\PluginInterface;
use Veneer\CoreBundle\Service\Workspace\App\AppInterface;
use Veneer\CoreBundle\Service\Workspace\Checkout\CheckoutInterface;
use Veneer\CoreBundle\Service\Workspace\Lifecycle\LifecycleInterface;

class DeploymentApp implements AppInterface, PluginInterface , LifecycleInterface
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

    public function getLinks(Request $request, $route)
    {
        $_bosh = $request->attributes->get('_bosh');

        switch ($route) {
            case 'veneer_bosh_deployment_summary':
                return [
                    (new Link('editor'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Edit Deployment')
                        ->setRoute(
                            $this->getAppRoute(),
                            [
                                'path' => sprintf(
                                    'bosh/deployment/%s/manifest.yml',
                                    $_bosh['deployment']['name']
                                ),
                            ]
                        ),
                ];
            case 'veneer_bosh_deployment_instancegroup_summary':
                return [
                    (new Link('editor'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Edit Instance Group')
                        ->setRoute(
                            'veneer_bosh_editor_app_deployment_edit',
                            [
                                'path' => sprintf(
                                    'bosh/deployment/%s/manifest.yml',
                                    $_bosh['deployment']['name']
                                ),
                                'property' => 'instance_groups['.$_bosh['instance_group']['job'].']',
                            ]
                        ),
                ];
        }
    }

    public function onCompile(CheckoutInterface $checkout, $path)
    {
        // TODO: Implement onCompile() method.
    }

    public function onPlan(CheckoutInterface $existing, CheckoutInterface $target, $path, array $compiled)
    {
        // TODO: Implement onPlan() method.
    }

    public function onApply(LoggerInterface $logger, array $compiled)
    {
        // TODO: Implement onApply() method.
    }
}
