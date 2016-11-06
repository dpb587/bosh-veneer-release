<?php

namespace Veneer\OpsBundle\Form\Type\CloudConfig;

use Symfony\Component\Form\FormBuilderInterface;
use Veneer\OpsBundle\Form\Type\AbstractDeploymentManifestPathType;

class CompilationType extends AbstractDeploymentManifestPathType
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
                'workers',
                'integer',
                [
                    'label' => 'Workers',
                    'veneer_help_html' => '<p>The maximum number of compilation VMs.</p>',
                ]
            )
            ->add(
                'network',
                'veneer_ops_deployment_network_manifestselector',
                [
                    'label' => 'Network',
                    'veneer_help_html' => '<p>References a valid network name defined in the Networks block. BOSH assigns network properties to compilation VMs according to the type and properties of the specified network.</p>',
                    'manifest' => $options['manifest'],
                    'manifest_path' => $options['manifest_path'],
                ]
            )
            ->add(
                'reuse_compilation_vms',
                'checkbox',
                [
                    'label' => 'Reuse Compilation VMs',
                    'veneer_help_html' => '<p>If enabled, compilation VMs are re-used when compiling packages. When disabled, BOSH creates a new compilation VM for each new package compilation and destroys the VM when compilation is complete.</p>',
                    'required' => false,
                ]
            )
            ->add(
                'cloud_properties',
                $this->cpi->getDeploymentResourcePoolFormType(),
                [
                    'label' => 'Cloud Properties',
                    'veneer_help_html' => '<p>Describes any IaaS-specific properties needed to create compilation VMs.</p>',
                ]
            )
            ;
    }

    public function getName()
    {
        return 'veneer_ops_editor_cloudconfig_compilation';
    }
}
