<?php

namespace Veneer\BoshEditorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;

class NetworkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                'text',
                [
                    'label' => 'Network Name',
                    'helptext' => 'DNS IP addresses for this network',
                    'required' => false,
                ]
            )
            ->add(
                'type_config',
                '@todo selector',
                [
                    'label' => 'Network Type',
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
        return 'veneer_bosheditor_network';
    }
}
