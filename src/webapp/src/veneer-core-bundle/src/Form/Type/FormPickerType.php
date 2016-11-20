<?php

namespace Veneer\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Veneer\CoreBundle\Form\DataTransformer\FormPickerTransformer;

class FormPickerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $keys = array_keys($options['forms']);

        $builder->add(
            'via',
            'choice',
            array(
                'choices' => array_combine($keys, $keys),
                'data' => current($keys),
                'expanded' => true,
            )
        );

        $builder->get('via')->setDataLocked(false);

        foreach ($options['forms'] as $name => $formset) {
            if (!$formset instanceof FormBuilderInterface) {
                $formset = $builder->create(
                    'via_' . $name,
                    $formset[0],
                    $formset[1]
                );
            }

            if ($formset->getName() != 'via_' . $name) {
                throw new \LogicException(sprintf('Expected builder form named %s', 'via_' . $name));
            }

            $formset->setRequired(false);
            $formset->setAttribute('validation_groups', function (FormInterface $form) use ($name) {
                return $form->getParent()->get('via')->getData() == $name ? 'Default' : 'None';
            });

            $builder->add($formset);
        }

        if ($options['transform_via']) {
            $builder->addModelTransformer(new FormPickerTransformer());
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setRequired(
            array(
                'forms',
            )
        );

        $options->setDefaults(
            array(
                'cascade_validation' => true,
                'transform_via' => true,
            )
        );
    }

    public function getName()
    {
        return 'veneer_core_form_picker';
    }
}
