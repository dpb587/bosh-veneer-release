<?php

namespace Veneer\OpsBundle\Service\Editor;

class CloudConfigFormHelper extends AbstractFormHelper
{
    public function lookup($manifest, $manifestPath, $path, $raw)
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
        } elseif ($raw) {
            $config = [
                'isset' => isset($manifest),
                'path' => '',
                'type' => 'veneer_ops_raw',
                'data' => $manifest,
                'title' => 'root',
            ];
        } else {
            throw new \InvalidArgumentException('Invalid concept');
        }

        if ($raw) {
            $formBuilder = $this->formFactory->createNamedBuilder('data', 'veneer_ops_raw');
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
