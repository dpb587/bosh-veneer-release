<?php

namespace Veneer\AwsCpiBundle\Form\Type\Api;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use SYmfony\Component\OptionsResolver\OptionsResolverInterface;

class Ec2InstanceTypeType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefaults([
            'choices' => [
                'c1.medium',
                'c1.xlarge',
                'c3.large',
                'c3.xlarge',
                'c3.2xlarge',
                'c3.4xlarge',
                'c3.8xlarge',
                'c4.large',
                'c4.xlarge',
                'c4.2xlarge',
                'c4.4xlarge',
                'c4.8xlarge',
                'cc1.4xlarge',
                'cc2.8xlarge',
                'cg1.4xlarge',
                'cr1.8xlarge',
                'd2.xlarge',
                'd2.2xlarge',
                'd2.4xlarge',
                'd2.8xlarge',
                'g2.2xlarge',
                'hi1.4xlarge',
                'hs1.8xlarge',
                'i2.xlarge',
                'i2.2xlarge',
                'i2.4xlarge',
                'i2.8xlarge',
                'm1.small',
                'm1.medium',
                'm1.large',
                'm1.xlarge',
                'm2.xlarge',
                'm2.2xlarge',
                'm2.4xlarge',
                'm3.2xlarge',
                'm3.medium',
                'm3.large',
                'm3.xlarge',
                'm4.large',
                'm4.xlarge',
                'm4.2xlarge',
                'm4.4xlarge',
                'm4.10xlarge',
                'r3.large',
                'r3.xlarge',
                'r3.2xlarge',
                'r3.4xlarge',
                'r3.8xlarge',
                't1.micro',
                't2.micro',
                't2.small',
                't2.medium',
            ],
        ]);
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'veneer_awscpi_api_ec2_instancetype';
    }
}
