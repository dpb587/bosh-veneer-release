<?php

namespace Veneer\BoshBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    public function summaryAction()
    {
        $attributesRaw = $this->container->get('doctrine.orm.bosh_entity_manager')
            ->getRepository('VeneerBoshBundle:DirectorAttributes')
            ->findAll();
        $attributes = [];

        foreach ($attributesRaw as $attributeRaw) {
            $attributes[$attributeRaw['name']] = $attributeRaw['value'];
        }

        return $this->renderApi(
            'VeneerBoshBundle:Index:summary.html.twig',
            [
                'data' => [
                    'name' => $this->container->getParameter('veneer_bosh.director_name'),
                    'attributes' => $attributes,
                ],
            ],
            [
                'def_nav' => $this->container->get('veneer_bosh.breadcrumbs')
                    ->add('BOSH Director'),
            ]
        );
    }
}
