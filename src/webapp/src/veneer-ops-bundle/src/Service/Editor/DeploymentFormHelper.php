<?php

namespace Veneer\OpsBundle\Service\Editor;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Veneer\BoshBundle\Service\DeploymentPropertySpecHelper;
use Veneer\BoshBundle\Model\DeploymentProperties;

class DeploymentFormHelper
{
    protected $formFactory;
    protected $deploymentPropertySpecHelper;

    public function __construct(FormFactoryInterface $formFactory, DeploymentPropertySpecHelper $deploymentPropertySpecHelper)
    {
        $this->formFactory = $formFactory;
        $this->deploymentPropertySpecHelper = $deploymentPropertySpecHelper;
    }

    public function lookup(array $manifest, $manifestPath, $path)
    {
        $pathMatch = null;

        if (preg_match('/^update(\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $config = [
                'isset' => isset($manifest['update']),
                'path' => '[update]',
                'type' => 'veneer_ops_deployment_update',
                'data' => isset($manifest['update']) ? $manifest['update'] : [],
                'title' => 'Update Settings',
            ];
        } elseif (preg_match('/^releases\[(?P<name>[^\]]*)\](\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $config = $this->lookupNamedIndex($manifest, 'releases', $pathMatch['name'], 'veneer_ops_deployment_release');
        } elseif (preg_match('/^instance_groups\[(?P<name>[^\]]*)\](\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $config = $this->lookupNamedIndex($manifest, 'instance_groups', $pathMatch['name'], 'veneer_ops_deployment_instancegroup');

            if (isset($pathMatch['subpath']) && preg_match('/^properties(\.(?P<subpath>.+))?$/', $pathMatch['subpath'], $pathMatch)) {
                $config = $this->lookupProperties(
                    new DeploymentProperties(isset($config['data']['properties']) ? $config['data']['properties'] : []),
                    $this->deploymentPropertySpecHelper->mergeTemplatePropertiesSpecs(
                        DeploymentPropertySpecHelper::collectReleaseJobs($manifest, $config['data']['name'])
                    ),
                    $config['path'] . '.properties',
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
        } else {
            throw new \InvalidArgumentException('Invalid concept');
        }

        $options = isset($config['options']) ? $config['options'] : [];
        $options['manifest'] = $manifest;
        $options['manifest_path'] = $manifestPath;

        $formBuilder = $this->formFactory->createNamedBuilder('data', $config['type'], null, $options);
        $formBuilder->setData($config['data']);

        $subpath = isset($pathMatch['subpath']) ? explode('.', $pathMatch['subpath']) : [];
        $this->filterFormBuilder($formBuilder, $subpath);

        $form = $formBuilder->getForm();

        return [
            'form' => $form,
            'isset' => $config['isset'],
            'path' => $config['path'],
            'data' => $config['data'],
            'title' => $config['title'],
        ];
    }

    protected function lookupProperties(DeploymentProperties $properties, array $propertiesSpec, $basepath, $path)
    {
        if (!isset($propertiesSpec[$path])) {
            throw new \InvalidArgumentException('Invalid property path: ' . $path);
        }

        return [
            'isset' => isset($properties[$path]),
            'path' => '[' . implode('][', explode('.', $basepath . '.' . $path)) . ']',
            'type' => 'veneer_ops_deployment_property',
            'options' => [
                'value_type' => 'veneer_core_yaml',
                'value_options' => [
                    'veneer_help_html' => isset($propertiesSpec[$path]['description']) ? $propertiesSpec[$path]['description'] : null,
                    'label' => ucwords(strtr(implode('', array_slice(explode('.', $path), -1)), '_', ' ')),
                ],
            ],
            'title' => 'Property (' . implode('.', array_slice(explode('.', $path), 0, -1)) . ')',
            'data' => isset($properties[$path]) ? $properties[$path] : null,
        ];
    }

    protected function filterFormBuilder(FormBuilderInterface $formBuilder, array $path)
    {
        if (0 == count($path)) {
            return;
        }

        $found = false;

        foreach ($formBuilder->all() as $name => $formBuilderChild) {
            if ($path[0] == $name) {
                $this->filterFormBuilder($formBuilderChild, array_slice($path, 1));
                $found = true;
            } else {
                $formBuilder->remove($name);
            }
        }

        if (!$found) {
            throw new \InvalidArgumentException(sprintf('Subpath "%s" does not exist', $path[0]));
        }
    }

    protected function lookupNamedIndex(array $manifest, $concept, $name, $type)
    {
        if (!isset($manifest[$concept])) {
            return [
                'isset' => false,
                'path' => '[' . $concept . '][0]',
                'type' => $type,
                'title' => ucwords(strtr($concept, '_', ' ')),
                'data' => [
                    'name' => $name,
                ],
            ];
        }

        if ('' != $name) {
            foreach ($manifest[$concept] as $conceptIdx => $conceptData) {
                if ($name != $conceptData['name']) {
                    continue;
                }

                return [
                    'isset' => true,
                    'path' => '[' . $concept . '][' . $conceptIdx . ']',
                    'type' => $type,
                    'title' => ucwords(strtr($concept, '_', ' ')),
                    'subtitle' => $name,
                    'data' => $conceptData,
                ];
            }
        }

        return [
            'isset' => false,
            'path' => '[' . $concept . '][' . count($manifest[$concept]) . ']',
            'type' => $type,
            'title' => ucwords(strtr($concept, '_', ' ')),
            'subtitle' => 'New',
            'data' => [
                'name' => $name,
            ],
        ];
    }
}
