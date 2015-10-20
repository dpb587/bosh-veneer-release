<?php

namespace Veneer\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Veneer\CoreBundle\Validator\Constraints as VeneerConstraints;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NetworkingCidrType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $options)
    {
        $options->setDefaults([
            'constraints' => [
                new VeneerConstraints\Cidr(),
            ],
        ]);
    }

    public function getName()
    {
        return 'veneer_core_networking_cidr';
    }

    public function getParent()
    {
        return 'text';
    }
}
