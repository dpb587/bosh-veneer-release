<?php

namespace Veneer\BoshEditorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;

class DiskPoolType extends AbstractType
{
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
                $options['cpi']->getNetworkDynamicForm(),
                [
                    'label' => 'Cloud Properties',
                    'helptext' => 'IaaS-specific properties needed to create disk.',
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
        return 'veneer_bosheditor_diskpool';
    }
}
