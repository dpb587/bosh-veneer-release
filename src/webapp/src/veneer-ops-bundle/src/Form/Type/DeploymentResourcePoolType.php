<?php

namespace Veneer\OpsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;
use Veneer\OpsBundle\Form\DataTransformer\ArrayToYamlTransformer;

class DeploymentResourcePoolType extends AbstractType
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
                'name',
                'text',
                [
                    'label' => 'Name',
                    'helptext' => 'A unique name used to identify and reference the resource pool',
                ]
            )
//            ->add(
//                'network',
//                'veneer_bosheditor_deployment_network',
//                [
//                    'label' => 'Network',
//                    'helptext' => 'References a valid network name defined in the Networks block. Newly created resource pool VMs use the described configuration.',
//                ]
//            )
            ->add(
                'size',
                'integer',
                [
                    'label' => 'Pool Size',
                    'helptext' => 'The number of VMs in the resource pool. If you omit this value, BOSH calculates the resource pool size based on the total number of job instances that belong to this resource pool. If you specify this value, BOSH requires that the size be at least as large as the total number of job instances using it.',
                    'required' => false,
                ]
            )
            ->add(
                'stemcell',
                'veneer_bosheditor_deployment_resourcepool_stemcell',
                [
                    'label' => 'Stemcell',
                    'helptext' => 'The stemcell used to create resource pool VMs.',
                ]
            )
            ->add(
                'cloud_properties',
                $this->cpi->getDeploymentResourcePoolFormType(),
                [
                    'label' => 'Cloud Properties',
                    'helptext' => 'IaaS-specific properties needed to create VMs.',
                ]
            )
            ->add(
                'env',
                'textarea',
                [
                    'label' => 'VM Environment',
                    'helptext' => 'Describes the VM environment and provides a specific VM environment to the CPI create_stemcell call. Environment data is available to BOSH Agents as VM settings.',
                    'required' => false,
                ]
            )
        ;

        $builder->get('env')->addModelTransformer(new ArrayToYamlTransformer());
    }

    public function getName()
    {
        return 'veneer_bosheditor_deployment_resourcepool';
    }
}
