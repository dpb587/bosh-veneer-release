<?php

namespace Veneer\AwsCpiBundle\Form\Type\Ops;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;

class DeploymentResourcePoolEphemeralDiskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'size',
                'integer',
                [
                    'label' => 'Disk Size',
                    'veneer_help_html' => '<p>Specifies the disk size in megabytes.</p>',
                ]
            )
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
            ;
    }

    public function getName()
    {
        return 'veneer_awscpi_ops_deployment_resourcepool_ephemeraldisk';
    }
}
