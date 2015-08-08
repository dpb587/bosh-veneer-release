<?php

namespace Bosh\WebBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Bosh\WebBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    public function summaryAction(Request $request)
    {
        return $this->renderApi(
            'BoshWebBundle:Index:summary.html.twig'
        );
    }
}
