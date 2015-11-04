<?php

namespace Veneer\OpsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;

class DeploymentNetworkDynamicType extends AbstractType
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
                $this->cpi->getDeploymentNetworkDynamicFormType(),
                [
                    'label' => 'Cloud Properties',
                    'veneer_help_html' => '<p>IaaS-specific properties for the network.</p>',
                ]
            )
            ;
    }

    public function getName()
    {
        return 'veneer_ops_deployment_network_dynamic';
    }
}
