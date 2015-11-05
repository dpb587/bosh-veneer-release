<?php

namespace Veneer\BoshBundle\Service;

use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\NoResultException;

class DeploymentPropertySpecHelper
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function mergeTemplatePropertiesSpecs(array $templates)
    {
        $er = $this->em->getRepository('VeneerBoshBundle:ReleaseVersionsTemplates');

        $specs = [];

        foreach ($templates as $template) {
            try {
                $lookup = $er->createQueryBuilder('rvt')
                    ->addSelect('t')
                    ->join('rvt.releaseVersion', 'rv')
                    ->join('rvt.template', 't')
                    ->join('rv.release', 'r')
                    ->andWhere(new Expr\Comparison('r.name', '=', ':release'))->setParameter('release', $template['release'])
                    ->andWhere(new Expr\Comparison('rv.version', '=', ':version'))->setParameter('version', $template['release_version'])
                    ->andWhere(new Expr\Comparison('t.name', '=', ':name'))->setParameter('name', $template['template'])
                    ->getQuery()
                    ->getSingleResult()
                    ;
            } catch (NoResultException $e) {
                continue;
            }

            $specs[$template['template']] = $lookup['template']['propertiesJsonAsArray'];
        }

        return self::mergeSpecs($specs);
    }

    static public function collectReleaseTemplates(array $manifest, $filterJob = null)
    {
        $releaseVersions = [];

        foreach ($manifest['releases'] as $release) {
            $releaseVersions[$release['name']] = $release['version'];
        }

        $collected = [];

        foreach ($manifest['jobs'] as $job) {
            if ((null !== $filterJob) && ($filterJob != $job['name'])) {
                continue;
            }

            foreach ($job['templates'] as $template) {
                $collected[$template['release'] . '/' . $template['name']] = [
                    'release' => $template['release'],
                    'release_version' => $releaseVersions[$template['release']],
                    'template' => $template['name'],
                ];
            }
        }

        return array_values($collected);
    }

    /**
     * Convert a spec-like list of property keys-values into a tree.
     */
    static public function convertSpecToTree(array $spec, $context = '')
    {
        $scope = [];

        foreach ($spec as $property => $value) {
            if (preg_match('/^' . preg_quote($context) . '(.+)/', $property, $match)) {
                $propcont = $match[1];
            } else {
                continue;
            }

            if (preg_match('/^([^\.]+)\..+$/', $propcont, $match)) {
                $scope[$match[1]]['property'] = $context . $match[1];
                $scope[$match[1]]['children'] = static::convertSpecToTree($spec, $context . $match[1] . '.');
            } else {
                $scope[$propcont]['property'] = $property;
                $scope[$propcont]['value'] = $value;
            }
        }

        ksort($scope);

        return $scope;
    }

    /**
     * Convert a tree list of properties into a spec-like list of property key-values.
     */
    static public function convertTreeToSpec(array $tree)
    {
        $scope = [];

        foreach ($tree as $subtree) {
            if (isset($subtree['children'])) {
                $scope = array_merge($scope, static::convertTreeToSpec($subtree['children']));
            }

            if (isset($subtree['value'])) {
                $scope[$subtree['property']] = $subtree['value'];
            }
        }

        return $scope;
    }

    public static function mergeSpecs(array $specs)
    {
        $merged = [];

        foreach ($specs as $ref => $spec) {
            foreach ($spec as $name => $property) {
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

                foreach ([ 'description', 'example', 'default', 'type' ] as $key) {
                    if (array_key_exists($key, $property)) {
                        if (!in_array($property[$key], $merged[$name]['_' . $key])) {
                            $merged[$name]['_' . $key][] = $property[$key];
                        }

                        unset($property[$key]);
                    }
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

    public static function createDocumentedYaml(array $spec) {
        return self::createDocumentedYamlFromTree(DeploymentPropertyHelper::convertSpecToTree($spec));
    }

    protected static function createDocumentedYamlFromTree(array $tree, $depth = 0)
    {
        $prefix = str_repeat('  ', $depth);

        $yaml = '';

        foreach ($properties as $name => $scope) {
            if (isset($scope['children'])) {
                $yaml .= $prefix . $name . ':' . "\n";
                $yaml .= $this->createDocumentedYaml($scope['children'], $depth + 1);
            } elseif (isset($scope['value'])) {
                $value = $scope['value'];

                if (isset($value['description'])) {
                    $yaml .= $prefix . '# ' . wordwrap($value['description'], 78 - (2 * $depth), "\n" . str_repeat('  ', $depth) . '# ') . "\n";
                }

                if (isset($value['example'])) {
                    if (isset($value['description'])) {
                        $yaml .= $prefix . '#' . "\n";
                    }

                    $yaml .= $prefix . '# Example:' . "\n";
                    $yaml .= $prefix . '# ' . str_replace("\n", "\n" . $prefix . '# ', trim(Yaml::dump([ $name => $value['example'] ], 8, 2))) . "\n";
                }

                if (array_key_exists('default', $value)) {
                    if (null === $value['default']) {
                        // easier to read
                        $yaml .= $prefix . $name . ': ~' . "\n";
                    } else {
                        $yaml .= $prefix . str_replace("\n", "\n" . $prefix, trim(Yaml::dump([ $name => $value['default'] ], 8, 2))) . "\n";
                    }
                } else {
                    $yaml .= $prefix . $name . ': @todo' . "\n";
                }

                $yaml .= "\n";
            }
        }

        return $yaml;
    }
}
