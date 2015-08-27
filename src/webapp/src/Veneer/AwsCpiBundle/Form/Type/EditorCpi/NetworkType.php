<?php

namespace Veneer\AwsCpiBundle\Form\Type\Cpi;

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
                'subnet',
                'veneer_awscpi_api_vpc_subnet',
                [
                    'label' => 'DNS',
                    'helptext' => 'Subnet ID in which instance will be created.',
                ]
            )
            ;
    }

    public function getName()
    {
        return 'veneer_awscpi_cpi_network';
    }
}
