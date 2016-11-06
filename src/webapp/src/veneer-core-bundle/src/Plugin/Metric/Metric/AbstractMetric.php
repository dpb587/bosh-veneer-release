<?php

namespace Veneer\CoreBundle\Plugin\Metric\Metric;

abstract class AbstractMetric implements MetricInterface
{
    protected function convertIntervalToSeconds(\DateInterval $interval)
    {
        $seconds = $interval->s;
        $seconds += 60 * $interval->i;
        $seconds += 3600 * $interval->h;
        $seconds += 86400 * $interval->d;

        return $seconds;
    }

    protected function normalizeEarly(\DateTime $start, \DateTime $end, \DateInterval $interval, array $data)
    {
        $intervalSeconds = $this->convertIntervalToSeconds($interval);
        $offset = new \DateInterval('PT'.($start->format('U') % $intervalSeconds).'S');

        $dn = clone $start;
        $dn->sub($offset);
        $de = clone $end;
        $de->sub($offset);

        $filled = [];

        while ($dn < $de) {
            $dv = $dn->format('U') * 1000;

            $filled[$dv] = [
                $dv,
                null,
            ];

            $dn->add($interval);
        }

        foreach ($data as $entry) {
            $x = $entry[0] - ($entry[0] % $intervalSeconds);
            $entry[0] = $x;
            $filled[$x] = $entry;
        }

        ksort($filled);

        return array_values($filled);
    }
}
