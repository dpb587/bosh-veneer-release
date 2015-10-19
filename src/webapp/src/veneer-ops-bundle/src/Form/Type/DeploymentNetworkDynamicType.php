<?php

namespace Veneer\OpsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;

class DeploymentNetworkDynamicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'dns',
                'collection',
                [
                    'type' => 'veneer_core_networking_cidr',
                    'label' => 'DNS',
                    'helptext' => 'DNS IP addresses for this network',
                    'required' => false,
                ]
            )
            ->add(
                'cloud_properties',
                $options['cpi']->getNetworkDynamicForm(),
                [
                    'label' => 'Cloud Properties',
                    'helptext' => 'IaaS-specific properties for the network.',
                ]
            )
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setRequired([
            'cpi',
        ]);
    }

    public function getName()
    {
        return 'veneer_bosheditor_deployment_network_dynamic';
    }
}
