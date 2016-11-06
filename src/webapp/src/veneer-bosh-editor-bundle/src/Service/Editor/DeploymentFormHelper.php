<?php

namespace Veneer\BoshEditorBundle\Service\Editor;

use Symfony\Component\Form\FormFactoryInterface;
use Veneer\BoshBundle\Service\DeploymentPropertySpecHelper;
use Veneer\BoshBundle\Model\DeploymentProperties;

class DeploymentFormHelper extends AbstractFormHelper
{
    protected $deploymentPropertySpecHelper;

    public function __construct(FormFactoryInterface $formFactory, DeploymentPropertySpecHelper $deploymentPropertySpecHelper)
    {
        parent::__construct($formFactory);

        $this->deploymentPropertySpecHelper = $deploymentPropertySpecHelper;
    }

    public function lookup(array $manifest, $manifestPath, $path, $raw)
    {
        $pathMatch = null;

        if (preg_match('/^update(\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $config = [
                'isset' => isset($manifest['update']),
                'path' => '[update]',
                'type' => 'veneer_bosh_editor_deployment_update',
                'data' => isset($manifest['update']) ? $manifest['update'] : [],
                'title' => 'Update Settings',
            ];
        } elseif (preg_match('/^releases\[(?P<name>[^\]]*)\](\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $config = $this->lookupNamedIndex($manifest, 'releases', $pathMatch['name'], 'veneer_bosh_editor_deployment_release');
        } elseif (preg_match('/^instance_groups\[(?P<name>[^\]]*)\](\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $config = $this->lookupNamedIndex($manifest, 'instance_groups', $pathMatch['name'], 'veneer_bosh_editor_deployment_instancegroup');

            if (isset($pathMatch['subpath']) && preg_match('/^properties(\.(?P<subpath>.+))?$/', $pathMatch['subpath'], $pathMatch)) {
                $config = $this->lookupProperties(
                    new DeploymentProperties(isset($config['data']['properties']) ? $config['data']['properties'] : []),
                    $this->deploymentPropertySpecHelper->mergeTemplatePropertiesSpecs(
                        DeploymentPropertySpecHelper::collectReleaseJobs($manifest, $config['data']['name'])
                    ),
                    $config['path'].'.properties',
                    $pathMatch['subpath']
                );

                $pathMatch['subpath'] = null;
            }
        } elseif (preg_match('/^properties(\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $config = $this->lookupProperties(
                new DeploymentProperties($manifest['properties']),
                $this->deploymentPropertySpecHelper->mergeTemplatePropertiesSpecs(
                    DeploymentPropertySpecHelper::collectReleaseJobs($manifest)
                ),
                'properties',
                $pathMatch['subpath']
            );

            $pathMatch['subpath'] = null;
        } elseif ($raw) {
            $config = [
                'isset' => isset($manifest),
                'path' => '',
                'type' => 'veneer_bosh_editor_raw',
                'data' => $manifest,
                'title' => 'root',
            ];
        } else {
            throw new \InvalidArgumentException('Invalid concept');
        }

        if ($raw) {
            $formBuilder = $this->formFactory->createNamedBuilder('data', 'veneer_bosh_editor_raw');
        } else {
            $options = isset($config['options']) ? $config['options'] : [];
            $options['manifest'] = $manifest;
            $options['manifest_path'] = $manifestPath;

            $formBuilder = $this->formFactory->createNamedBuilder('data', $config['type'], null, $options);

            $subpath = isset($pathMatch['subpath']) ? explode('.', $pathMatch['subpath']) : [];
            $this->filterFormBuilder($formBuilder, $subpath);
        }

        $formBuilder->setData($config['data']);
        $form = $formBuilder->getForm();

        return [
            'form' => $form,
            'isset' => $config['isset'],
            'path' => $config['path'],
            'data' => $config['data'],
            'title' => $config['title'],
        ];
    }
}
