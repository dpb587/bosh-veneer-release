<?php

namespace Veneer\BoshEditorBundle\Form\DataTransformer;

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

        $wrap = [
            'name' => $value['name'],
        ];

        if (isset($value['type'])) {
            $wrap['network'] = [
                $value['type'] => $network,
            ];
        }

        return $wrap;
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
