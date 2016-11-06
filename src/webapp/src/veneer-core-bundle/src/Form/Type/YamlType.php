<?php

namespace Veneer\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Veneer\CoreBundle\Form\DataTransformer\YamlTransformer;

class YamlType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new YamlTransformer());
    }

    public function getParent()
    {
        return 'textarea';
    }

    public function getName()
    {
        return 'veneer_core_yaml';
    }
}
