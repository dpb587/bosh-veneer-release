<?php

namespace Veneer\BoshEditorBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

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
