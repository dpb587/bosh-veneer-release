<?php

namespace Veneer\CoreBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VeneerFormExtension extends AbstractTypeExtension
{
    public function getExtendedType()
    {
        return 'field';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'veneer_advanced' => false,
        ]);

        $resolver->setOptional([
            'veneer_help_html',
            'veneer_help_links',
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['veneer_advanced'] = $options['veneer_advanced'];
        $view->vars['veneer_help_html'] = isset($options['veneer_help_html']) ? $options['veneer_help_html'] : null;
        $view->vars['veneer_help_links'] = isset($options['veneer_help_links']) ? $options['veneer_help_links'] : null;
    }
}
