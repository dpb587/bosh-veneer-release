<?php

namespace Veneer\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NetworkingIpType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefaults([
            'constraints' => [
                new Constraints\Ip(),
            ],
        ]);
    }

    public function getName()
    {
        return 'veneer_core_networking_ip';
    }

    public function getParent()
    {
        return 'text';
    }
}
