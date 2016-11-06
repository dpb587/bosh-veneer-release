<?php

namespace Veneer\BoshEditorBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class RawTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (null === $value) {
            return;
        }

        return [
            'raw' => $value,
        ];
    }

    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        } elseif (!is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        return $value['raw'];
    }
}
