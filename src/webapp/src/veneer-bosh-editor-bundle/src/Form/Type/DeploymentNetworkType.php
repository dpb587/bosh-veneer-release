<?php

namespace Veneer\BoshEditorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;

class DeploymentNetworkType extends AbstractType
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
                'text',
                [
                    'label' => 'Network Type',
                ]
            )
            ;
    }

    public function getName()
    {
        return 'veneer_bosheditor_deployment_network';
    }
}
