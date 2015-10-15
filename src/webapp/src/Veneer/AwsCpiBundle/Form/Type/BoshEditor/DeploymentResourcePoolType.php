<?php

namespace Veneer\AwsCpiBundle\Form\Type\BoshEditor;

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
                    'helptext' => 'Type of the instance.',
                ]
            )
            ->add(
                'availability_zone',
                'veneer_awscpi_api_ec2_availabilityzone',
                [
                    'label' => 'Availability Zone',
                    'helptext' => 'Availability zone to use for creating instances.',
                ]
            )
            ->add(
                'key_name',
                'veneer_awscpi_api_ec2_keypair',
                [
                    'label' => 'Key Pair Name',
                    'helptext' => 'Defaults to key pair name specified by default_key_name in global CPI settings.',
                    'required' => false,
                ]
            )
            ->add(
                'spot_bid_price',
                'money',
                [
                    'label' => 'Spot Bid Price',
                    'helptext' => 'Bid price in dollars.',
                    'required' => false,
                ]
            )
            ->add(
                'elbs',
                'collection',
                [
                    'label' => 'ELB Names',
                    'type' => 'veneer_awscpi_api_ec2_elb',
                    'helptext' => 'ELB names that should be attached to created VMs.',
                    'required' => false,
                ]
            )
            ->add(
                'ephemeral_disk',
                'veneer_awscpi_bosheditor_deployment_resourcepool_ephemeraldisk',
                [
                    'label' => 'Ephemeral Disk',
                    'helptext' => 'EBS backed ephemeral disk of custom size for when instance storage is not large enough or not available for selected instance type.',
                    'required' => false,
                ]
            )
            ;
    }

    public function getName()
    {
        return 'veneer_awscpi_bosheditor_deployment_resourcepool';
    }
}
