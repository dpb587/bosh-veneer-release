<?php

namespace Veneer\BoshBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\BoshBundle\Model\TaskTracker;

class TaskController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return TaskALLController::defNav($nav, $_bosh)
            ->add(
                '#'.$_bosh['task']['id'],
                [
                    'veneer_bosh_task_summary' => [
                        'task' => $_bosh['task']['id'],
                    ],
                ]
            )
        ;
    }

    public function summaryAction(Request $request, $_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:Task:summary.html.twig',
            [
                'data' => $_bosh['task'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
                'continue' => $request->query->get('continue'),
            ]
        );
    }

    public function eventsAction(Request $request, $_bosh)
    {
        $events = $this->container->get('veneer_bosh.api')->getTaskOutput($_bosh['task']['id'], 0, 'event');

        if ($request->query->get('step')) {
            $events['data'] = array_slice($events['data'], 0, $request->query->get('step'));
        }

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
