<?php

namespace Veneer\AwsCpiBundle\Form\Type\Editor;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DiskTypeType extends AbstractType
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
        return 'veneer_aws_cpi_editor_disktype';
    }
}
