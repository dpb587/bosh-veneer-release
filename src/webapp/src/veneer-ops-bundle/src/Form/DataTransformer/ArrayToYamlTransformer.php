<?php

namespace Veneer\OpsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Yaml\Yaml;

class ArrayToYamlTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (null === $value) {
            return;
        } elseif (!is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        return Yaml::dump($value, 8);
    }

    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        } elseif (!is_string($value)) {
            throw new TransformationFailedException('Expected a string.');
        }

        return Yaml::parse($value);
    }
}
