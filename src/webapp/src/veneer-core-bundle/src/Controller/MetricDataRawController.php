<?php

namespace Veneer\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;

class MetricDataRawController extends AbstractController
{
    public function rangeAction(Request $request)
    {
        if (!$request->query->has('start')) {
            throw new HttpException(400, 'Missing start parameter');
        } elseif (!$request->query->has('end')) {
            throw new HttpException(400, 'Missing end parameter');
        } elseif (!$request->query->has('interval')) {
            throw new HttpException(400, 'Missing interval parameter');
        } elseif (!$request->query->has('interval')) {
            throw new HttpException(400, 'Missing interval parameter');
        } elseif (!$request->query->has('statistic')) {
            throw new HttpException(400, 'Missing statistic parameter');
        }

        $start = new \DateTime($request->query->get('start'));
        $end = new \DateTime($request->query->get('end'));
        $interval = new \DateInterval('P' . $request->query->get('interval'));
        $statistic = $request->query->get('statistic');
        $metricName = $request->query->get('metric');

        $metric = $this->container->get('veneer_core.plugin.metric.context.resolver')->resolve($metricName);

        return new JsonResponse(
            [
                'meta' => [
                    'start' => $start->format('c'),
                    'end' => $end->format('c'),
                    'metric' => $metricName,
                    'chart' => $metric->getChartDefaults(),
                ],
                'data' => $metric->load($start, $end, $interval, $statistic),
            ]
        );
    }
}
