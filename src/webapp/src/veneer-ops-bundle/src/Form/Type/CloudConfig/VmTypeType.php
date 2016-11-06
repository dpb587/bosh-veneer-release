<?php

namespace Veneer\OpsBundle\Form\Type\CloudConfig;

use Symfony\Component\Form\FormBuilderInterface;
use Veneer\BoshBundle\Service\Cpi\CpiFactory;
use Veneer\OpsBundle\Form\Type\AbstractDeploymentManifestPathType;

class VmTypeType extends AbstractDeploymentManifestPathType
{
    protected $cpi;

    public function __construct(CpiFactory $cpi)
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
                'cloud_properties',
                $this->cpi->lookup()->getEditorFormType('vmtype'),
                [
                    'label' => 'Cloud Properties',
                    'veneer_help_html' => '<p>IaaS-specific properties needed to create VMs.</p>',
                ]
            )
            ->add(
                'env',
                'veneer_core_yaml',
                [
                    'label' => 'VM Environment',
                    'veneer_help_html' => '<p>Describes the VM environment and provides a specific VM environment to the CPI create_stemcell call. Environment data is available to BOSH Agents as VM settings.</p>',
                    'required' => false,
                ]
            )
        ;
    }

    public function getName()
    {
        return 'veneer_ops_editor_cloudconfig_vmtype';
    }
}
