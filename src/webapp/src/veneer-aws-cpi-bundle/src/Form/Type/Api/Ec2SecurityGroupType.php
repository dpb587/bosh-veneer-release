<?php

namespace Veneer\AwsCpiBundle\Form\Type\Api;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;

class Ec2SecurityGroupType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefaults([
            'constraints' => [
                new Constraints\Regex([
                    'match' => false,
                    'pattern' => '/^sg-[a-f0-9]{8}$/',
                    'message' => 'This value must be the security group name, not the "sg-a1b2c3d4" ID.'
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
        return 'veneer_awscpi_api_ec2_securitygroup';
    }
}
