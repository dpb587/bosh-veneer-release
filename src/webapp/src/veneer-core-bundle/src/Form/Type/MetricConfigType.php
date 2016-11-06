<?php

namespace Veneer\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MetricConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'start',
                'datetime'
            )
            ->add(
                'end',
                'datetime'
            )
            ->add(
                'interval',
                'string'
            )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefaults(
            array(
                'csrf_protection' => false,
            )
        );
    }

    public function getName()
    {
        return 'veneer_core_metric_config';
    }
}
