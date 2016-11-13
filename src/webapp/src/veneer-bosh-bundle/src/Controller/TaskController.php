<?php

namespace Veneer\BoshBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\BoshBundle\Model\TaskTracker;
use Veneer\BoshBundle\Plugin\RequestContext\Annotations as BoshContext;

/**
 * @BoshContext\Task
 */
class TaskController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return TaskALLController::defNav($nav)
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

    public function summaryAction(Request $request, Context $_bosh)
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

    public function eventsAction(Request $request, Context $_bosh)
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
