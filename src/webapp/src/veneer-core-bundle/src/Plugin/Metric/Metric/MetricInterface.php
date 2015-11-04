<?php

namespace Veneer\CoreBundle\Plugin\Metric\Metric;

interface MetricInterface
{
    public function load(\DateTime $start, \DateTime $end, \DateInterval $interval, $statistic);
}
