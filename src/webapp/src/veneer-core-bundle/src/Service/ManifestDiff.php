<?php

namespace Veneer\CoreBundle\Service;

use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

class ManifestDiff
{
    public static function diff($a, $b)
    {
        $dumpA = Yaml::dump($a, 8, 2);
        $dumpB = Yaml::dump($b, 8, 2);

        $fileA = tempnam(sys_get_temp_dir(), 'diffa');
        file_put_contents($fileA, rtrim($dumpA) . "\n");

        $fileB = tempnam(sys_get_temp_dir(), 'diffb');
        file_put_contents($fileB, rtrim($dumpB) . "\n");

        $p = new Process('diff ' . escapeshellarg($fileA) . ' ' . escapeshellarg($fileB));
        $p->run();

        unlink($fileA);
        unlink($fileB);

        return explode("\n", $p->getOutput());
    }
    /*
    public static function diff(array $a, array $b, $indent = 0, $context = null)
    {
        $diff = [];

        $aKeys = array_keys($a);
        $bKeys = array_keys($b);

        $addedKeys = array_diff($bKeys, $aKeys);
        $removedKeys = array_diff($aKeys, $bKeys);

        foreach (array_intersect($aKeys, $bKeys) as $key) {
            if ($a[$key] == $b[$key]) {
                continue;
            } elseif (is_array($a[$key]) && is_array($b[$key])) {
                if (static::isIntegerIndex($a[$key]) && static::isIntegerIndex($b[$key])) {
                    $values = array_merge($a[$key], $b[$key]);

                    $labeledIndex = static::findLabeledIndex($values);

                    foreach ($values as $value) {

                    }
                } else {
                    $diff = array_merge(
                        $diff,
                        static::diff($a[$key], $b[$key], $indent + 1, str_repeat('  ', $indent, $key . ':'))
                    );
                }
            } else {
                $diff = static::append($diff, null, $key, $indent);
                $diff = static::append($diff, 'removed', $a[$key], $indent + 1);
                $diff = static::append($diff, 'added', $b[$key], $indent + 1);
            }
        }

        foreach ($addedKeys as $key) {
            $diff = array_merge(
                $diff,
                array_map(
                    function ($line) {
                        return [$line, 'added'];
                    },
                    explode("\n", preg_replace('/^/', str_repeat('  ', $indent), Yaml::dump($b[$key], 8, 2)))
                )
            );
        }

        foreach ($removedKeys as $key) {
            $diff = array_merge(
                $diff,
                array_map(
                    function ($line) {
                        return [$line, 'removed'];
                    },
                    explode("\n", preg_replace('/^/', str_repeat('  ', $indent), Yaml::dump($a[$key], 8, 2)))
                )
            );
        }

        if (0 < count($diff) && null !== $context) {
            array_unshift($diff, is_string($context) ? $context : $context($a, $b));
        }

        return $diff;
    }

    private static function append(array $diff, $status, $data, $indent)
    {
        return array_merge(
            $diff,
            array_map(
                function ($line) use ($status) {
                    return [$status, $line];
                },
                explode("\n", preg_replace('/^/', str_repeat('  ', $indent), Yaml::dump($data, 8, 2)))
            )
        );
    }

    private static function isIntegerIndex(array $array)
    {
        return count($array) == array_filter(array_keys($array), 'is_int');
    }

    private static function findLabeledIndex(array $values)
    {
        foreach ([ 'name', 'range' ] as $label) {
            $match = array_filter(
                $values,
                function (array $value) use ($label) {
                    return isset($value[$label]);
                }
            );

            if (count($match) == count($values)) {
                return $label;
            }
        }

        return null;
    }
    */
}
