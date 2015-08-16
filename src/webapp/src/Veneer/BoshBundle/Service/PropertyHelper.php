<?php

namespace Veneer\BoshBundle\Service;

use Symfony\Component\Yaml\Yaml;

class PropertyHelper
{
    public function createPropertyTree(array $properties, $context = null) {
        $scope = [];

        foreach ($properties as $name => $property) {
            if (preg_match('/^' . preg_quote($context) . '(.+)/', $name, $match)) {
                $propcont = $match[1];
            } else {
                continue;
            }

            if (preg_match('/^([^\.]+)\..+$/', $propcont, $match)) {
                $scope[$match[1]] = [
                    'children' => $this->createPropertyTree($properties, $context . $match[1] . '.'),
                ];
            } else {
                $scope[$propcont] = [
                    'value' => $property,
                ];
            }
        }

        ksort($scope);

        return $scope;
    }

    public function createDocumentedYaml(array $properties, $depth = 0) {
        $prefix = str_repeat('  ', $depth);

        $yaml = '';

        foreach ($properties as $name => $scope) {
            if (isset($scope['children'])) {
                $yaml .= $prefix . $name . ':' . "\n";
                $yaml .= $this->createDocumentedYaml($scope['children'], $depth + 1);

                continue;
            }

            $scope = $scope['value'];

            if (isset($scope['description'])) {
                $yaml .= $prefix . '# ' . wordwrap($scope['description'], 78 - (2 * $depth), "\n" . str_repeat('  ', $depth) . '# ') . "\n";
            }

            if (isset($scope['example'])) {
                if (isset($scope['description'])) {
                    $yaml .= $prefix . '#' . "\n";
                }

                $yaml .= $prefix . '# Example:' . "\n";
                $yaml .= $prefix . '# ' . str_replace("\n", "\n" . $prefix . '# ', trim(Yaml::dump([ $name => $scope['example'] ], 8, 2))) . "\n";
            }

            if (array_key_exists('default', $scope)) {
                if (null === $scope['default']) {
                    // easier to read
                    $yaml .= $prefix . $name . ': ~' . "\n";
                } else {
                    $yaml .= $prefix . str_replace("\n", "\n" . $prefix, trim(Yaml::dump([ $name => $scope['default'] ], 8, 2))) . "\n";
                }
            } else {
                $yaml .= $prefix . $name . ': @todo' . "\n";
            }

            $yaml .= "\n";
        }

        return $yaml;
    }
}
