<?php

namespace Veneer\OpsBundle\Service;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormBuilderInterface;

class DeploymentFormHelper
{
    protected $formFactory;
    protected $manifest;

    public function __construct(FormFactoryInterface $formFactory, array $manifest)
    {
        $this->formFactory = $formFactory;
        $this->manifest = $manifest;
    }

    public function lookup($path)
    {
        $pathMatch = null;

        if (preg_match('/^compilation(\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $formType = 'veneer_ops_deployment_compilation';
            $data = [
                'isset' => isset($this->manifest['compilation']),
                'path' => '[compilation]',
                'data' => isset($this->manifest['compilation']) ? $this->manifest['compilation'] : [],
            ];
        } elseif (preg_match('/^update(\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $formType = 'veneer_ops_deployment_update';
            $data = [
                'isset' => isset($this->manifest['update']),
                'path' => '[update]',
                'data' => isset($this->manifest['update']) ? $this->manifest['update'] : [],
            ];
        } elseif (preg_match('/^disk_pools\[(?P<name>[^\]]*)\](\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $formType = 'veneer_ops_deployment_diskpool';
            $data = $this->lookupNamedIndex('disk_pools', $pathMatch['name']);
        } elseif (preg_match('/^networks\[(?P<name>[^\]]*)\](\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $formType = 'veneer_ops_deployment_network';
            $data = $this->lookupNamedIndex('networks', $pathMatch['name']);
        } elseif (preg_match('/^resource_pools\[(?P<name>[^\]]*)\](\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $formType = 'veneer_ops_deployment_resourcepool';
            $data = $this->lookupNamedIndex('resource_pools', $pathMatch['name']);
        } elseif (preg_match('/^releases\[(?P<name>[^\]]*)\](\.(?P<subpath>.+))?$/', $path, $pathMatch)) {
            $formType = 'veneer_ops_deployment_release';
            $data = $this->lookupNamedIndex('releases', $pathMatch['name']);
        } else {
            throw new \InvalidArgumentException('Invalid concept');
        }

        $formBuilder = $this->formFactory->createNamedBuilder('data', $formType);
        $formBuilder->setData($data['data']);

        $subpath = isset($pathMatch['subpath']) ? explode('.', $pathMatch['subpath']) : [];
        $this->filterFormBuilder($formBuilder, $subpath);

        $form = $formBuilder->getForm();

        return [
            'form' => $form,
            'isset' => $data['isset'],
            'path' => $data['path'],
            'data' => $data['data'],
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

    protected function lookupNamedIndex($concept, $name)
    {
        if (!isset($this->manifest[$concept])) {
            return [
                'isset' => false,
                'path' => '[' . $concept . '][0]',
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
                    'data' => $conceptData,
                ];
            }
        }

        return [
            'isset' => false,
            'path' => '[' . $concept . '][' . count($this->manifest[$concept]) . ']',
            'data' => [
                'name' => $name,
            ],
        ];
    }
}
