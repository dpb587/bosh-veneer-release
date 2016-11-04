<?php

namespace Veneer\OpsBundle\Service\Editor;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Veneer\BoshBundle\Service\DeploymentPropertySpecHelper;
use Veneer\BoshBundle\Model\DeploymentProperties;

class CloudConfigFormHelper
{
    protected $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function lookup(array $manifest, $manifestPath, $path)
    {
        $pathMatch = null;

        if (preg_match('/^compilation(\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $config = [
                'isset' => isset($manifest['compilation']),
                'path' => '[compilation]',
                'type' => 'veneer_ops_editor_cloudconfig_compilation',
                'data' => isset($manifest['compilation']) ? $manifest['compilation'] : [],
                'title' => 'Compilation Settings',
            ];
        } elseif (preg_match('/^azs\[(?P<name>[^\]]*)\](\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $config = $this->lookupNamedIndex($manifest, 'azs', $pathMatch['name'], 'veneer_ops_editor_cloudconfig_availabilityzone');
        } elseif (preg_match('/^disk_types\[(?P<name>[^\]]*)\](\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $config = $this->lookupNamedIndex($manifest, 'disk_types', $pathMatch['name'], 'veneer_ops_editor_cloudconfig_disktype');
        } elseif (preg_match('/^vm_types\[(?P<name>[^\]]*)\](\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $config = $this->lookupNamedIndex($manifest, 'vm_types', $pathMatch['name'], 'veneer_ops_editor_cloudconfig_vmtype');
        } elseif (preg_match('/^vm_extensions\[(?P<name>[^\]]*)\](\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $config = $this->lookupNamedIndex($manifest, 'vm_extensions', $pathMatch['name'], 'veneer_ops_editor_cloudconfig_vmextension');
        } elseif (preg_match('/^networks\[(?P<name>[^\]]*)\](\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $config = $this->lookupNamedIndex($manifest, 'networks', $pathMatch['name'], 'veneer_ops_editor_cloudconfig_network');
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
