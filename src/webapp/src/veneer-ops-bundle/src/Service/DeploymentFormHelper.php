<?php

namespace Veneer\OpsBundle\Service;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Veneer\BoshBundle\Service\PropertyHelper;

class DeploymentFormHelper
{
    protected $formFactory;
    protected $propertyHelper;

    public function __construct(FormFactoryInterface $formFactory, PropertyHelper $propertyHelper)
    {
        $this->formFactory = $formFactory;
        $this->propertyHelper = $propertyHelper;
    }

    public function lookup(array $manifest, $path)
    {
        $pathMatch = null;

        if (preg_match('/^compilation(\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $config = [
                'isset' => isset($this->manifest['compilation']),
                'path' => '[compilation]',
                'type' => 'veneer_ops_deployment_compilation',
                'data' => isset($this->manifest['compilation']) ? $this->manifest['compilation'] : [],
                'title' => 'Compilation Settings',
            ];
        } elseif (preg_match('/^update(\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $config = [
                'isset' => isset($this->manifest['update']),
                'path' => '[update]',
                'type' => 'veneer_ops_deployment_update',
                'data' => isset($this->manifest['update']) ? $this->manifest['update'] : [],
                'title' => 'Update Settings',
            ];
        } elseif (preg_match('/^disk_pools\[(?P<name>[^\]]*)\](\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $config = $this->lookupNamedIndex('disk_pools', $pathMatch['name'], 'veneer_ops_deployment_diskpool');
        } elseif (preg_match('/^networks\[(?P<name>[^\]]*)\](\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $config = $this->lookupNamedIndex('networks', $pathMatch['name'], 'veneer_ops_deployment_network');
        } elseif (preg_match('/^resource_pools\[(?P<name>[^\]]*)\](\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $config = $this->lookupNamedIndex('resource_pools', $pathMatch['name'], 'veneer_ops_deployment_resourcepool');
        } elseif (preg_match('/^releases\[(?P<name>[^\]]*)\](\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $config = $this->lookupNamedIndex('releases', $pathMatch['name'], 'veneer_ops_deployment_release');
        } elseif (preg_match('/^jobs\[(?P<name>[^\]]*)\](\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $config = $this->lookupNamedIndex('jobs', $pathMatch['name'], 'veneer_ops_deployment_job');
        } elseif (preg_match('/^properties(\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $config = $this->lookupProperties(
                $manifest['properties'],
                $this->propertyHelper->mergeManifestPropertySets($manifest),
                'properties',
                $pathMatch['subpath']
            );
            $pathMatch['subpath'] = null;
        } else {
            throw new \InvalidArgumentException('Invalid concept');
        }

        $formBuilder = $this->formFactory->createNamedBuilder('data', $config['type'], null, isset($config['options']) ? $config['options'] : []);
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

    protected function lookupProperties(array $properties, array $flattenedPropertyConfig, $basepath, $path)
    {
        $flattenedProperties = $this->propertyHelper->flattenProperties($properties);

        if (!isset($flattenedPropertyConfig[$path])) {
            throw new \InvalidArgumentException('Invalid property path: ' . $path);
        }

        return [
            'isset' => isset($flattenedProperties[$path]),
            'path' => '[' . implode('][', explode('.', $basepath . '.' . $path)) . ']',
            'type' => 'veneer_ops_deployment_property',
            'options' => [
                'value_type' => 'veneer_core_yaml',
                'value_options' => [
                    'helptext' => isset($flattenedPropertyConfig[$path]['description']) ? $flattenedPropertyConfig[$path]['description'] : null,
                    'label' => ucwords(strtr(implode('', array_slice(explode('.', $path), -1)), '_', ' ')),
                ],
            ],
            'title' => 'Property (' . implode('.', array_slice(explode('.', $path), 0, -1)) . ')',
            'data' => isset($flattenedProperties[$path]) ? $flattenedProperties[$path] : null,
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

    protected function lookupNamedIndex($concept, $name, $type)
    {
        if (!isset($this->manifest[$concept])) {
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
            foreach ($this->manifest[$concept] as $conceptIdx => $conceptData) {
                if ($name != $conceptData['name']) {
                    continue;
                }

                return [
                    'isset' => true,
                    'path' => '[' . $concept . '][' . $conceptIdx . ']',
                    'type' => $type,
                    'title' => ucwords(strtr($concept, '_', ' ')) . ' (' . $name . ')',
                    'data' => $conceptData,
                ];
            }
        }

        return [
            'isset' => false,
            'path' => '[' . $concept . '][' . count($this->manifest[$concept]) . ']',
            'type' => $type,
            'title' => 'New ' . ucwords(strtr($concept, '_', ' ')),
            'data' => [
                'name' => $name,
            ],
        ];
    }
}
