<?php

namespace Veneer\BoshEditorBundle\Controller;

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
use Veneer\BoshEditorBundle\Form\Type\DeploymentResourcePoolType;
use Veneer\BoshEditorBundle\Service\DeploymentEditor;

class TestController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, array $_bosh)
    {
        return DeploymentController::defNav($nav, $_bosh);
    }

    public function editorAction(Request $request, $_bosh)
    {
        $manifest = Yaml::parse(file_get_contents(__DIR__ . '/manifest.yml'));
        //$manifest = Yaml::parse($_bosh['deployment']['manifest']);

        $path = $request->query->get('path');

        $editor = new DeploymentEditor($this->container->get('form.factory'), $manifest);
        $editorProfile = $editor->lookup($path);

        return $this->renderApi(
            'VeneerBoshEditorBundle:Test:editor.html.twig',
            [
                'form' => $editorProfile['form']->createView(),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
}
