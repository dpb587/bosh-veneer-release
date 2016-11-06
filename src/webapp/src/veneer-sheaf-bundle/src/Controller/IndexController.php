<?php

namespace Veneer\SheafBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    public function summaryAction()
    {
        return $this->redirectToRoute('veneer_sheaf_listingALL_index');
    }
}
