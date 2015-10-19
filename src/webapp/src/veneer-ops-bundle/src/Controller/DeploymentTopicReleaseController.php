<?php

namespace Veneer\OpsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\PropertyAccess\PropertyAccess;

class DeploymentTopicReleaseController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, array $_bosh)
    {
        DeploymentController::defNav($nav, $_bosh)->add('Release');
    }

    public function summaryAction(Request $request, $_bosh)
    {
        $manifest = Yaml::parse(file_get_contents(__DIR__ . '/manifest.yml'));
        //$manifest = Yaml::parse($_bosh['deployment']['manifest']);

        $path = $request->query->get('path');

        $accessor = PropertyAccess::createPropertyAccessor();
        $data = $accessor->getValue($manifest, $path);

        if (preg_match('/^\[disk_pools\]/', $path)) {
            $formType = 'veneer_bosheditor_deployment_diskpool';
        } elseif (preg_match('/^\[resource_pools\]/', $path)) {
            $formType = 'veneer_bosheditor_deployment_resourcepool';
        } else {
            throw new \UnexpectedValueException('@todo missing a config form');
        }

        $form = $this->container->get('form.factory')->createNamed('data', $formType);
        $form->setData($data);

        return $this->renderApi(
            'VeneerOpsBundle:Test:editor.html.twig',
            [
                'form' => $form->createView(),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
}
