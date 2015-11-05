<?php

namespace Veneer\AwsCpiBundle\Form\Type\Ops;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;

class DeploymentNetworkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'subnet',
                'veneer_awscpi_api_vpc_subnet',
                [
                    'label' => 'Subnet',
                    'veneer_help_html' => '<p>Subnet ID in which instance will be created.</p>',
                ]
            )
            ->add(
                'security_groups',
                'collection',
                [
                    'label' => 'Security Groups',
                    'type' => 'veneer_awscpi_api_ec2_securitygroup',
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            )
            ;
    }

    public function getName()
    {
        return 'veneer_awscpi_ops_deployment_network';
    }
}
