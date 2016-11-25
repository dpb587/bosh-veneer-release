<?php

namespace Veneer\LogsearchBundle\Service;

use Elastica\Client;
use Elastica\Request;
use Veneer\CoreBundle\Plugin\RequestContext\Context;

class ElasticsearchHelper
{
    protected $elasticsearch;
    protected $indexTemplate;

    protected $timezoneUtc;
    protected $timezoneUs;

    public function __construct(Client $elasticsearch, $indexTemplate)
    {
        $this->elasticsearch = $elasticsearch;
        $this->indexTemplate = $indexTemplate;
        $this->timezoneUtc = new \DateTimeZone('UTC');
        $this->timezoneUs = new \DateTimeZone(ini_get('date.timezone'));
    }

    public function getClient()
    {
        return $this->elasticsearch;
    }

    public function generateContextFilters(Context $context)
    {
        $filters = [];

//        $filters[] = [
//            '@source.bosh_director' => '@todo',
//        ];

        if (isset($context['deployment'])) {
            $filters[] = [
                'term' => [
                    '@source.bosh_deployment' => $context['deployment']['name'],
                ],
            ];

            if (isset($context['vm'])) {
                $filters[] = [
                    'term' => [
                        '@source.bosh_job' => $context['vm']['applySpecJsonAsArray']['job']['name'].'/'.$context['vm']['applySpecJsonAsArray']['index'],
                    ],
                ];
            } elseif (isset($context['instance'])) {
                $filters[] = [
                    'term' => [
                        '@source.bosh_job' => $context['instance']['job'].'/'.$context['instance']['index'],
                    ],
                ];
            }
        }

        return ['and' => $filters];
    }

    public function getPathIndices(\DateTime $ds, \DateTime $de)
    {
        $interval = new \DateInterval('PT1H');

        $dn = clone $ds;
        $dn->setTimezone($this->timezoneUtc);

        $dez = clone $de;
        $dez->setTimezone($this->timezoneUtc);

        preg_match('/\[([^\]]+)\]/', $this->indexTemplate, $match);

        $indices = [];

        while ($dn < $dez) {
            $indices[str_replace($match[0], $dn->format($match[1]), $this->indexTemplate)] = true;

            $dn->add($interval);
        }

        $indices[str_replace($match[0], $dez->format($match[1]), $this->indexTemplate)] = true;

        return implode(',', array_keys($indices));
    }

    public function request(\DateTime $ds, \DateTime $de, $url, $data = null)
    {
        return $this->elasticsearch->request(
            $this->getPathIndices($ds, $de).'/'.$url,
            'POST',
            $data
        )->getData();
    }

    public function reduceDateHistogram(\DateTime $ds, \DateTime $de, \DateInterval $di, array $buckets, $default = null)
    {
        return array_map(
            function (array $v) {
                return [
                    'x' => $v['key'],
                    'y' => $v['value']['value'],
                ];
            },
            $this->fillDateHistogram($ds, $de, $di, $buckets, ['value' => $default])
        );
    }

    public function generateTimestampFilters(\DateTime $ds, \DateTime $de)
    {
        return [
            'range' => [
                '@timestamp' => [
                    'gte' => $ds->format('c'),
                    'le' => $de->format('c'),
                ],
            ],
        ];
    }

    public function fillDateHistogram(\DateTime $ds, \DateTime $de, \DateInterval $di, array $buckets, array $default = [])
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

    public function changeUtcOffset($value, $format)
    {
        return \DateTime::createFromFormat($format, $value, $this->timezoneUtc)
            ->setTimezone($this->timezoneUs)
            ->format($format)
            ;
    }

    public function changeUtcOffsetDateHistogram(array $entries)
    {
        foreach ($entries as &$entry) {
            $entry['time'] = $this->changeUtcOffset($entry['time'] / 1000, 'U');
        }

        return $entries;
    }
}
