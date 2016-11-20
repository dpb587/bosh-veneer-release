<?php

namespace Veneer\BoshEditorBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

class DeploymentInstanceGroupType extends AbstractDeploymentManifestPathType
{
    protected $cpi;

    public function __construct($cpi)
    {
        $this->cpi = $cpi;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                'text',
                [
                    'label' => 'Name',
                    'veneer_help_html' => '<p>A unique name used to identify and reference the resource pool</p>',
                ]
            )
            ->add(
                'instances',
                'integer',
                [
                    'label' => 'Instances',
                ]
            )
            ->add(
                'resource_pool',
                'veneer_bosh_editor_deployment_resourcepool_manifestselector',
                [
                    'label' => 'Resource Pool',
                    'manifest' => $options['manifest'],
                    'manifest_file' => $options['manifest_file'],
                ]
            )
            ->add(
                'persistent_disk_pool',
                'veneer_bosh_editor_deployment_diskpool_manifestselector',
                [
                    'label' => 'Disk Pool',
                    'required' => false,
                    'manifest' => $options['manifest'],
                    'manifest_file' => $options['manifest_file'],
                ]
            )
            ->add(
                'jobs',
                'veneer_bosh_editor_deployment_instancegroup_templates',
                [
                    'label' => 'Templates',
                    'manifest' => $options['manifest'],
                    'manifest_file' => $options['manifest_file'],
                ]
            )
            ->add(
                'networks',
                'collection',
                [
                    'label' => 'Networks',
                    'type' => 'veneer_bosh_editor_deployment_instancegroup_network',
                    'options' => [
                        'manifest' => $options['manifest'],
                        'manifest_file' => $options['manifest_file'],
                    ],
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            )
        ;
    }

    public function getName()
    {
        return 'veneer_bosh_editor_deployment_instancegroup';
    }
}
