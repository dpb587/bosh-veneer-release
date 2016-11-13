<?php

namespace Veneer\BoshBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\BoshBundle\Plugin\RequestContext\Annotations as BoshContext;

/**
 * @BoshContext\Event
 */
class EventController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return EventALLController::defNav($nav, $_bosh)
            ->add(
                '#'.$_bosh['event']['id'],
                [
                    'veneer_bosh_event_summary' => [
                        'event' => $_bosh['event']['id'],
                    ],
                ]
            )
        ;
    }

    public function summaryAction(Context $_bosh)
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
