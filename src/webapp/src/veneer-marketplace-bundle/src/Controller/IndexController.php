<?php

namespace Veneer\MarketplaceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    public function summaryAction()
    {
        return $this->redirectToRoute('veneer_marketplace_marketplaceALL_index');
    }
}
