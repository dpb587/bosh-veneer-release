<?php

namespace Veneer\OpsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class DeploymentNetworkManualSubnetType extends AbstractType
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
                    'allow_add' => true,
                    'allow_delete' => true,
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
                    'allow_add' => true,
                    'allow_delete' => true,
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
                    'allow_add' => true,
                    'allow_delete' => true,
                    'helptext' => 'Static IPs and/or IP ranges. BOSH assigns IPs from this range to jobs requesting static IPs. Only IPs specified here can be used for static IP reservations.',
                    'required' => false,
                ]
            )
            ->add(
                'cloud_properties',
                $this->cpi->getDeploymentNetworkManualForm(),
                [
                    'label' => 'Cloud Properties',
                    'helptext' => 'IaaS-specific properties for the subnet.',
                ]
            )
            ;
    }

    public function getName()
    {
        return 'veneer_ops_deployment_network_manual_subnet';
    }
}
