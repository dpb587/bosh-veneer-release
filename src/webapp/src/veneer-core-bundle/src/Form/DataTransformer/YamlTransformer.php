<?php

namespace Veneer\CoreBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Yaml\Yaml;

class YamlTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        return Yaml::dump($value, 8);
    }

    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        return Yaml::parse($value);
    }
}
