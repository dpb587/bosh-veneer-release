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
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return $nav->add(
            '#' . $_bosh['task']['id'],
            [
                'veneer_bosh_task_summary' => [
                    'task' => $_bosh['task']['id'],
                ],
            ],
            [
                'glyphicon' => 'tasks',
                'expanded' => true,
            ]
        );
    }

    public function summaryAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:Task:summary.html.twig',
            [
                'data' => $_bosh['task'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }

    public function eventsAction($_bosh)
    {
        $events = $this->container->get('veneer_bosh.api')->getTaskOutput($_bosh['task']['id'], 0, 'event');

        $tracker = new TaskTracker($events['data']);

        return $this->renderApi(
            'VeneerBoshBundle:Task:events.html.twig',
            [
                'tracker_state' => $tracker->getState(),
                'tracker_errors' => $tracker->getErrors(),
            ]
        );
    }
}
