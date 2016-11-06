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

        if (is_string($value) && (false !== strpos($value, "\n"))) {
            return '|'."\n".$value;
        }

        return Yaml::dump($value, 8);
    }

    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        if (false !== strpos($value, "\n")) {
            $exp = explode("\n", $value);

            if (preg_match('/^|\s*$/', $exp[0])) {
                return implode("\n", array_slice($exp, 1));
            }
        }

        return Yaml::parse($value);
    }
}
