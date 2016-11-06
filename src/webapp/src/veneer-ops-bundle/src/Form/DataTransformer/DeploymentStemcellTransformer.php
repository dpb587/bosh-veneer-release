<?php

namespace Veneer\OpsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DeploymentStemcellTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (null === $value) {
            return;
        } elseif (!is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        return [
            'picker' => (isset($value['name']) ? $value['name'] : '').'/'.(isset($value['version']) ? $value['version'] : ''),
        ];
    }

    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        } elseif (!is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        $split = explode('/', $value['picker'], 2);

        return [
            'name' => $split[0],
            'version' => $split[1],
        ];
    }
}
