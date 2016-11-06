<?php

namespace Veneer\BoshEditorBundle\Form\Type\CloudConfig;

use Symfony\Component\Form\FormBuilderInterface;
use Veneer\BoshBundle\Service\Cpi\CpiFactory;
use Veneer\BoshEditorBundle\Form\Type\AbstractDeploymentManifestPathType;

class NetworkDynamicType extends AbstractDeploymentManifestPathType
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
                'dns',
                'collection',
                [
                    'type' => 'veneer_core_networking_cidr',
                    'label' => 'DNS',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'veneer_help_html' => '<p>DNS IP addresses for this network</p>',
                    'required' => false,
                ]
            )
            ->add(
                'cloud_properties',
                $this->cpi->lookup()->getEditorFormType('network'),
                [
                    'label' => 'Cloud Properties',
                    'veneer_help_html' => '<p>IaaS-specific properties for the network.</p>',
                ]
            )
            ;
    }

    public function getName()
    {
        return 'veneer_bosh_editor_editor_cloudconfig_network_dynamic';
    }
}
