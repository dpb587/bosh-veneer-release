<?php

namespace Veneer\OpsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;

class DeploymentCompilationType extends AbstractType
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
                'workers',
                'integer',
                [
                    'label' => 'Workers',
                    'helptext' => 'The maximum number of compilation VMs.',
                ]
            )
//            ->add(
//                'network',
//                'veneer_bosheditor_deployment_network',
//                [
//                    'label' => 'Network',
//                    'helptext' => 'References a valid network name defined in the Networks block. BOSH assigns network properties to compilation VMs according to the type and properties of the specified network.',
//                ]
//            )
            ->add(
                'reuse_compilation_vms',
                'checkbox',
                [
                    'label' => 'Reuse Compilation VMs',
                    'helptext' => 'If enabled, compilation VMs are re-used when compiling packages. When disabled, BOSH creates a new compilation VM for each new package compilation and destroys the VM when compilation is complete.',
                    'required' => false,
                ]
            )
            ->add(
                'cloud_properties',
                $this->cpi->getDeploymentResourcePoolFormType(),
                [
                    'label' => 'Cloud Properties',
                    'helptext' => 'Describes any IaaS-specific properties needed to create compilation VMs.',
                ]
            )
            ;
    }

    public function getName()
    {
        return 'veneer_bosheditor_deployment_compilation';
    }
}
