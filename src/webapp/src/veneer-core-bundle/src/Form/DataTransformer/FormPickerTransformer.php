<?php

namespace Veneer\CoreBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class FormPickerTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (!$value) {
            return null;
        }

        reset($value);

        return [
            'via' => key($value),
            'via_' . key($value) => current($value),
        ];
    }

    public function reverseTransform($value)
    {
        if (!$value) {
            return null;
        }

        return [
            $value['via'] => $value['via_' . $value['via']],
        ];
    }
}
