<?php

namespace Veneer\HubBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    public function summaryAction()
    {
        return $this->redirectToRoute('veneer_hub_hubALL_index');
    }
}
