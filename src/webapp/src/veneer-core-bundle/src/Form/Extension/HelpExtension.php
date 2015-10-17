<?php

namespace Veneer\CoreBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HelpExtension extends AbstractTypeExtension
{
    public function getExtendedType()
    {
        return 'field';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional([
            'helptext',
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['helptext'] = isset($options['helptext']) ? $options['helptext'] : null;
    }
}