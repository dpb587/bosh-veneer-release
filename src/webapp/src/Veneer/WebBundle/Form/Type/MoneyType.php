<?php

namespace Veneer\WebBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType as BaseMoneyType;

class MoneyType extends BaseMoneyType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(
            array(
                'currency' => 'USD',
            )
        );
    }
}