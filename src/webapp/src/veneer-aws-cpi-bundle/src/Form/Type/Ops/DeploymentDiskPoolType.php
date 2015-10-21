<?php

namespace Veneer\AwsCpiBundle\Form\Type\Ops;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;

class DeploymentDiskPoolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'type',
                'choice',
                [
                    'label' => 'Disk Type',
                    'helptext' => 'Type of the disk',
                    'choices' => [
                        'standard' => 'Magnetic (standard)',
                        'gp2' => 'General Purpose SSD (gp2)',
                    ],
                    'required' => false,
                ]
            )
            ->add(
                'encrypted',
                'checkbox',
                [
                    'label' => 'Turns on EBS volume encryption for this persistent disk.',
                    'helptext' => 'VM root and ephemeral disk are not encrypted.',
                ]
            )
            ;
    }

    public function getName()
    {
        return 'veneer_awscpi_ops_deployment_diskpool';
    }
}
