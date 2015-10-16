<?php

namespace Veneer\AwsCpiBundle\Form\Type\Api;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Yaml\Yaml;

class Ec2InstanceTypeType extends AbstractType
{
    static private $config;

    public function __construct()
    {
        if (null === static::$config) {
            static::$config = Yaml::parse(file_get_contents(__DIR__ . '/../../../Resources/aws/instance-types.yml'));
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefaults([
            'choices' => array_keys(static::$config['types']),
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
