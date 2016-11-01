<?php

namespace Veneer\BoshBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;

class JobRestartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'force',
                'checkbox',
                [
                    'label' => 'Proceed even when there are other manifest changes',
                    'required' => false,
                ]
            )
            ->add(
                'skip_drain',
                'checkbox',
                [
                    'label' => 'Skip running drain script',
                    'required' => false,
                ]
            )
            ;
    }

    public function getName()
    {
        return 'veneer_bosh_job_restart';
    }
}
