<?php

namespace Veneer\AwsCpiBundle\Form\Type\Api;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;

class VpcSubnetType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefaults([
            'constraints' => [
                new Constraints\Regex([
                    'pattern' => '/^subnet-[a-f0-9]{8}$/',
                    'message' => 'This value must be like "subnet-a1b2c3d4".'
                ]),
            ],
        ]);
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'veneer_awscpi_api_vpc_subnet';
    }
}
