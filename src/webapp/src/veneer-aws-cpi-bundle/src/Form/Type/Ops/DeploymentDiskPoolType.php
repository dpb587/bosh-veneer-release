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
                    'required' => false,
                    'veneer_help_html' => '<p>VM root and ephemeral disk are not encrypted.</p>',
                ]
            )
            ;
    }

    public function getName()
    {
        return 'veneer_awscpi_ops_deployment_diskpool';
    }
}
