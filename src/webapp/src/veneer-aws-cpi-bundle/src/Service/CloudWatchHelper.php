<?php

namespace Veneer\AwsCpiBundle\Service;

class CloudWatchHelper
{
    static public function fillDateHistogram(\DateTime $ds, \DateTime $de, \DateInterval $di, array $buckets, array $default = [])
    {
        $filled = [];
        $dn = clone $ds;
        $de = clone $de;

        while ($dn < $de) {
            $dv = $dn->format('U') * 1000;

            $filled[$dv] = $default;
            $filled[$dv]['key'] = $dv;

            $dn->add($di);
        }

        foreach ($buckets as $entry) {
            $filled[$entry['key']] = $entry;
        }

        ksort($filled);

        return array_values($filled);
    }
}
