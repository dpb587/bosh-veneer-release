<?php

namespace Veneer\BoshBundle\Service;

use Symfony\Component\Yaml\Yaml;

class PropertyHelper
{
    public function mergePropertySets(array $propertySets)
    {
        $merged = [];

        foreach ($propertySets as $ref => $propertySet) {
            foreach ($propertySet as $name => $property) {
                if (!isset($merged[$name])) {
                    $merged[$name] = [
                        '_ref' => [],
                        '_description' => [],
                        '_example' => [],
                        '_default' => [],
                        '_type' => [],
                        '_other' => [],
                    ];
                }

                if (!in_array($ref, $merged[$name]['_ref'])) {
                    $merged[$name]['_ref'][] = $ref;
                }

                if (array_key_exists('description', $property)) {
                    if (!in_array($property['description'], $merged[$name]['_description'])) {
                        $merged[$name]['_description'][] = $property['description'];
                    }

                    unset($property['description']);
                }

                if (array_key_exists('example', $property)) {
                    if (!in_array($property['example'], $merged[$name]['_example'])) {
                        $merged[$name]['_example'][] = $property['example'];
                    }

                    unset($property['example']);
                }

                if (array_key_exists('default', $property)) {
                    if (!in_array($property['default'], $merged[$name]['_default'])) {
                        $merged[$name]['_default'][] = $property['default'];
                    }

                    unset($property['default']);
                }

                if (array_key_exists('type', $property)) {
                    foreach ((array) $property['type'] as $type) {
                        if (!in_array($type, $merged[$name]['type'])) {
                            $merged[$name]['_type'][] = $type;
                        }
                    }

                    unset($property['type']);
                }

                foreach ($property as $k => $v) {
                    $merged[$name]['_other'][$k][] = $v;
                }
            }
        }

        // consolidate singletons in case we pretend this is a regular property tree

        foreach ($merged as $name => $property) {
            foreach ([ 'description', 'example', 'default', 'type' ] as $key) {
                if (1 == count($property['_' . $key])) {
                    $merged[$name][$key] = $property['_' . $key][0];
                }
            }
        }

        return $merged;
    }

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
                    'full_key' => $name,
                    'value' => $property,
                ];
            }
        }

        ksort($scope);

        return $scope;
    }

    public function flattenPropertyTree(array $propertyTree, $context = null)
    {
        $scope = [];

        foreach ($propertyTree as $k => $v) {
            if (is_array($v)) {
                // if this is a hash, go deep
                foreach ($v as $k2 => $v2) {
                    if (is_string($k2)) {
                        $scope = array_merge($scope, $this->flattenPropertyTree($v, $context . $k . '.'));

                        continue 2;
                    }
                }
            }

            $scope[$context . $k] = $v;
        }

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
