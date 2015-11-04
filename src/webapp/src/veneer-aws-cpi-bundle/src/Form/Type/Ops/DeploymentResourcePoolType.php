<?php

namespace Veneer\AwsCpiBundle\Form\Type\Ops;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;

class DeploymentResourcePoolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'instance_type',
                'veneer_awscpi_api_ec2_instancetype',
                [
                    'label' => 'Instance Type',
                ]
            )
            ->add(
                'availability_zone',
                'veneer_awscpi_api_ec2_availabilityzone',
                [
                    'label' => 'Availability Zone',
                    'veneer_help_html' => '<p>Availability zone to use for creating instances.</p>',
                ]
            )
            ->add(
                'key_name',
                'veneer_awscpi_api_ec2_keypair',
                [
                    'label' => 'Key Pair Name',
                    'veneer_help_html' => '<p>Defaults to key pair name specified by default_key_name in global CPI settings.</p>',
                    'required' => false,
                ]
            )
            ->add(
                'spot_bid_price',
                'money',
                [
                    'label' => 'Spot Bid Price',
                    'required' => false,
                ]
            )
            ->add(
                'elbs',
                'collection',
                [
                    'label' => 'ELB Names',
                    'type' => 'veneer_awscpi_api_ec2_elb',
                    'veneer_help_html' => '<p>ELB names that should be attached to created VMs.</p>',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'required' => false,
                ]
            )
            ->add(
                'ephemeral_disk',
                'veneer_awscpi_ops_deployment_resourcepool_ephemeraldisk',
                [
                    'label' => 'Ephemeral Disk',
                    'veneer_help_html' => '<p>EBS backed ephemeral disk of custom size for when instance storage is not large enough or not available for selected instance type.</p>',
                    'required' => false,
                ]
            )
            ;
    }

    public function getName()
    {
        return 'veneer_awscpi_ops_deployment_resourcepool';
    }
}
