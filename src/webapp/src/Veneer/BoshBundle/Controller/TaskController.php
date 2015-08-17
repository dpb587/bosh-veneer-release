<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\WebBundle\Controller\AbstractController;
use Veneer\WebBundle\Service\Breadcrumbs;
use Veneer\BoshBundle\Model\TaskTracker;

class TaskController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_context)
    {
        return $nav->add(
            '#' . $_context['task']['id'],
            [
                'veneer_bosh_task_summary' => [
                    'task' => $_context['task']['id'],
                ],
            ],
            [
                'glyphicon' => 'tasks',
                'expanded' => true,
            ]
        );
    }

    public function summaryAction($_context)
    {
        return $this->renderApi(
            'VeneerBoshBundle:Task:summary.html.twig',
            [
                'data' => $_context['task'],
                'endpoints' => $this->container->get('veneer_bosh.plugin_factory')->getEndpoints('bosh/task', $_context),
                'references' => $this->container->get('veneer_bosh.plugin_factory')->getUserReferenceLinks('bosh/task', $_context),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_context),
            ]
        );
    }

    public function trackerAction($_context)
    {
        $events = $this->container->get('veneer_bosh.api')->getTaskOutput($_context['task']['id'], 0, 'event');

        $tracker = new TaskTracker($events['data']);

        return $this->renderApi(
            'VeneerBoshBundle:Task:tracker.html.twig',
            [
                'tracker_state' => $tracker->getState(),
                'tracker_errors' => $tracker->getErrors(),
            ]
        );
    }
}
