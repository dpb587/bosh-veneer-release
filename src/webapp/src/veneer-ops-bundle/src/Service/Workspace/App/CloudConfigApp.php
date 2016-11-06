<?php

namespace Veneer\OpsBundle\Service\Workspace\App;

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
use Veneer\OpsBundle\Service\ManifestBuilder\ManifestBuilderInterface;

class CloudConfigApp implements AppInterface, PluginInterface, LifecycleInterface
{
    protected $manifestBuilder;
    protected $boshApiClient;

    public function __construct(ManifestBuilderInterface $manifestBuilder, DirectorApiClient $boshApiClient)
    {
        $this->manifestBuilder = $manifestBuilder;
        $this->boshApiClient = $boshApiClient;
    }

    public function getAppTitle()
    {
        return 'Cloud Config Editor';
    }

    public function getAppDescription()
    {
        return 'Edit the various aspects of your cloud config';
    }

    public function getAppRoute()
    {
        return 'veneer_ops_workspace_app_cloudconfig_summary';
    }

    public function getLinks(Request $request, $route)
    {
        switch ($route) {
            case 'veneer_bosh_cloudconfig_summary':
                return [
                    (new Link('ops_edit'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Edit Cloud Config')
                        ->setRoute(
                            $this->getAppRoute(),
                            [
                                'path' => 'bosh/cloud-config/manifest.yml',
                            ]
                        ),
                ];
        }
    }

    public function onCompile(CheckoutInterface $checkout, $path)
    {
        $physicalCheckout = $checkout->getPhysicalCheckout();

        return [
            'action' => 'update',
            'manifest' => $this->manifestBuilder->build($physicalCheckout->getPhysicalPath(), $path),
        ];
    }

    public function onPlan(CheckoutInterface $existing, CheckoutInterface $target, $path, array $compiled)
    {
        $response = $this->boshApiClient->get('/cloud_configs?limit=1');
        $responseJson = json_decode($response->getBody(), true);
        $oldManifest = isset($responseJson[0]) ? $responseJson[0]['properties'] : null;
        $newManifest = $compiled['manifest'];

        $plan = [
            'view' => 'VeneerOpsBundle:Plugin/WorkspaceApp/CloudConfigApp:plan.html.twig',
            'data' => [
                'action' => 'unchanged',
            ],
        ];

        if ($oldManifest != $newManifest) {
            $plan['data'] = [
                'action' => 'update',
                'old' => $oldManifest,
                'new' => $newManifest,
                'diff' => ManifestDiff::diff(Yaml::parse($oldManifest), Yaml::parse($newManifest)),
            ];
        }

        return $plan;
    }

    public function onApply(LoggerInterface $logger, array $compiled)
    {
        if ($compiled['action'] == 'update') {
            $this->boshApiClient->post(
                '/cloud_configs',
                [
                    'headers' => [
                        'content-type' => 'text/yaml',
                    ],
                    'body' => $compiled['manifest'],
                ]
            );
        } else {
            throw new \LogicException('Unexpected action: '.$compiled['action']);
        }
    }
}
