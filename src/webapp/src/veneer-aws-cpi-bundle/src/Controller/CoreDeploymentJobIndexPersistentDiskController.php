<?php

namespace Veneer\AwsCpiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\AwsCpiBundle\Service\CloudWatchHelper;

class CoreDeploymentJobIndexPersistentDiskController extends AbstractController
{
    public function cpiAction(array $_bosh)
    {
        return $this->renderApi(
            'VeneerAwsCpiBundle:CoreDeploymentJobIndexPersistentDisk:cpi.html.twig',
            [
                'properties' => $_bosh['persistent_disk']['cloudPropertiesJsonAsArray'],
            ]
        );
    }

    public function cloudwatchBytesStatsAction(array $_bosh)
    {
        return $this->handleStats($_bosh, [ 'VolumeReadBytes', 'VolumeWriteBytes' ], 'cloudwatch-bytes-stats');
    }

    public function cloudwatchOpsStatsAction(array $_bosh)
    {
        return $this->handleStats($_bosh, [ 'VolumeReadOps', 'VolumeWriteOps' ], 'cloudwatch-ops-stats');
    }

    public function cloudwatchIdleStatsAction(array $_bosh)
    {
        return $this->handleStats($_bosh, [ 'VolumeIdleTime' ], 'cloudwatch-idle-stats');
    }

    public function cloudwatchQueueStatsAction(array $_bosh)
    {
        return $this->handleStats($_bosh, [ 'VolumeQueueLength' ], 'cloudwatch-queue-stats');
    }

    public function cloudwatchTimeStatsAction(array $_bosh)
    {
        return $this->handleStats($_bosh, [ 'VolumeTotalReadTime', 'VolumeTotalWriteTime' ], 'cloudwatch-time-stats');
    }

    protected function handleStats(array $_bosh, array $metricNames, $template)
    {
        $di = new \DateInterval('PT5M');
        $dis = 300;

        $ds = new \DateTime('-6 hours');
        $ds->sub(new \DateInterval('PT' . ($ds->format('i') % 5) . 'M' . $ds->format('s') . 'S'));

        $de = new \DateTime('now');
        $de->sub(new \DateInterval('PT' . $de->format('s') . 'S'));

        $client = $this->container->get('veneer_aws_cpi.api.cloudwatch');

        $collected = [];

        foreach ($metricNames as $metricName) {
            $result = $client->getMetricStatistics([
                'Namespace'  => 'AWS/EBS',
                'MetricName' => $metricName,
                'Dimensions' => [
                    [
                        'Name' => 'VolumeId',
                        'Value' => $_bosh['persistent_disk']['diskCid'],
                    ],
                ],
                'StartTime'  => $ds->format('c'),
                'EndTime'    => $de->format('c'),
                'Period'     => $dis,
                'Statistics' => [
                    'Average',
                ],
            ]);

            $mapped = [];

            foreach ($result['Datapoints'] as $datapoint) {
                $mapped[] = [
                    'key' => (new \DateTime($datapoint['Timestamp']))->format('U') * 1000,
                    'y' => $datapoint['Average'] / ($dis / 60),
                ];
            }

            $collected[$metricName] = CloudWatchHelper::fillDateHistogram(
                $ds,
                $de,
                $di,
                $mapped,
                [
                    'y' => 0,
                ]
            );
        }

        return $this->renderApi(
            'VeneerAwsCpiBundle:CoreDeploymentJobIndexPersistentDisk:' . $template . '.html.twig',
            [
                'series' => $collected,
            ]
        );
    }
}
