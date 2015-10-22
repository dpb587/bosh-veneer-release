<?php

namespace Veneer\OpsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Yaml\Yaml;

class DeploymentPropertyTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        return [
            'value' => $value,
        ];
    }

    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        return $value['value'];
    }
}
