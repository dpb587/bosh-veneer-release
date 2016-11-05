<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class EventController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return EventALLController::defNav($nav, $_bosh)
            ->add(
                '#' . $_bosh['event']['id'],
                [
                    'veneer_bosh_event_summary' => [
                        'event' => $_bosh['event']['id'],
                    ],
                ]
            )
        ;
    }

    public function summaryAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:Event:summary.html.twig',
            [
                'data' => $_bosh['event'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
}
