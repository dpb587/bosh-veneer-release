<?php

namespace Veneer\BoshEditorBundle\Form\Type\CloudConfig;

use Symfony\Component\Form\FormBuilderInterface;
use Veneer\BoshBundle\Service\Cpi\CpiFactory;
use Veneer\BoshEditorBundle\Form\Type\AbstractDeploymentManifestPathType;

class AvailabilityZoneType extends AbstractDeploymentManifestPathType
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
        ;
    }

    public function getName()
    {
        return 'veneer_bosh_editor_editor_cloudconfig_availabilityzone';
    }
}
