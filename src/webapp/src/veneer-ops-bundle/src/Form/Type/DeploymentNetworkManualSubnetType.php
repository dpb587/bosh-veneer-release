<?php

namespace Veneer\OpsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class DeploymentNetworkManualSubnetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'range',
                'veneer_core_networking_cidr',
                [
                    'label' => 'Subnet CIDR',
                    'helptext' => 'Subnet IP range that includes all IPs from this subnet',
                ]
            )
            ->add(
                'gateway',
                'veneer_core_networking_ip',
                [
                    'label' => 'Gateway',
                    'helptext' => 'Subnet Gateway IP',
                ]
            )
            ->add(
                'dns',
                'collection',
                [
                    'type' => 'veneer_core_networking_cidr',
                    'label' => 'DNS',
                    'helptext' => 'DNS IP addresses for this subnet',
                    'required' => false,
                ]
            )
            ->add(
                'reserved',
                'collection',
                [
                    'type' => 'veneer_core_networking_cidr',
                    'label' => 'Reserved IPs',
                    'helptext' => 'Reserved IPs and/or IP ranges. BOSH does not assign IPs from this range to any VM',
                    'required' => false,
                ]
            )
            ->add(
                'static',
                'collection',
                [
                    'type' => 'veneer_core_networking_cidr',
                    'label' => 'Static IPs',
                    'helptext' => 'static IPs and/or IP ranges. BOSH assigns IPs from this range to jobs requesting static IPs. Only IPs specified here can be used for static IP reservations.',
                    'required' => false,
                ]
            )
            ->add(
                'cloud_properties',
                $options['cpi']->getNetworkManualSubnetForm(),
                [
                    'label' => 'Cloud Properties',
                    'helptext' => 'IaaS-specific properties for the subnet.',
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
        return 'veneer_bosheditor_deployment_network_manual';
    }
}
