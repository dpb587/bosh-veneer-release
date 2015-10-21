<?php

namespace Veneer\OpsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;

class DeploymentDiskPoolType extends AbstractType
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
                    'label' => 'Disk Pool Name',
                    'helptext' => 'A unique name used to identify and reference the disk pool.',
                ]
            )
            ->add(
                'disk_size',
                'integer',
                [
                    'label' => 'Disk Size',
                    'helptext' => 'Size of the disk in megabytes.',
                ]
            )
            ->add(
                'cloud_properties',
                $this->cpi->getDeploymentDiskPoolFormType(),
                [
                    'label' => 'Cloud Properties',
                    'helptext' => 'IaaS-specific properties needed to create disk.',
                ]
            )
            ;
    }

    public function getName()
    {
        return 'veneer_ops_deployment_diskpool';
    }
}
