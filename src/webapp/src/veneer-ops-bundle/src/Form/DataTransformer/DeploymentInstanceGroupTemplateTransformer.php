<?php

namespace Veneer\OpsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DeploymentInstanceGroupTemplateTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (null === $value) {
            return;
        } elseif (!is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        return array_map(
            function (array $value) {
                return $value['release'].'/'.$value['name'];
            },
            $value
        );
    }

    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        } elseif (!is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        return array_map(
            function (array $value) {
                $split = explode('/', $value, 2);

                return [
                    'release' => $split[0],
                    'name' => $split[1],
                ];
            },
            $value
        );
    }
}
