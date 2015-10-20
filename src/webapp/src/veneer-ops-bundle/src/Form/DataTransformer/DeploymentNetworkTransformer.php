<?php

namespace Veneer\OpsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DeploymentNetworkTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (null === $value) {
            return;
        } elseif (!is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        $network = $value;
        unset($network['name'], $network['type']);

        return [
            'name' => $value['name'],
            'network' => [
                $value['type'] => $network,
            ],
        ];
    }

    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        } elseif (!is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        return array_merge(
            current($value['network']),
            [
                'name' => $value['name'],
                'type' => key($value['network']),
            ]
        );
    }
}