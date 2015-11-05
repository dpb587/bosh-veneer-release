<?php

namespace Veneer\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;

class MetricBrowseController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $all = [];

        $start = $request->query->get('start', '-6 hours');
        $end = $request->query->get('end', 'now');
        $interval = $request->query->get('interval', 'T5M');

        foreach ((array) $request->query->get('series') as $series) {
            if (!isset($series['metric'])) {
                throw new HttpException(400, 'Missing metric parameter');
            } elseif (!isset($series['statistic'])) {
                throw new HttpException(400, 'Missing statistic parameter');
            }

            $parsed = [
                'url' => $this->container->get('router')->generate(
                    'veneer_core_metric_data_raw_range',
                    [
                        'start' => $start,
                        'end' => $end,
                        'interval' => $interval,
                        'metric' => $series['metric'],
                        'statistic' => $series['statistic'],
                    ]
                ),
                'options' => array_merge(
                    $request->query->get('defaults', []),
                    isset($series['defaults']) ? $series['defaults'] : []
                ),
                'transform' => isset($series['transform']) ? explode(',', $series['transform']) : null,
            ];

            $all[] = $parsed;
        }

        return $this->renderApi(
            'VeneerCoreBundle:MetricChart:index.html.twig',
            [
                'title' => $request->query->get('title', 'Chart'),
                'refs' => $all,
            ]
        );
    }
}
