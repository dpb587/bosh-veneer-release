<?php

namespace Veneer\CoreBundle\Controller;

use Monolog\Logger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\CoreBundle\Service\Workspace\Lifecycle\LifecycleInterface;

class WorkspaceRepoApplyController extends AbstractController
{
    public function indexAction($ref)
    {
        if (false !== strpos($ref, '..')) {
            list($refLeft, $refRight) = explode('..', $ref, 2);
        } else {
            $refLeft = 'live';
            $refRight = $ref;
        }

        $repo = $this->container->get('veneer_core.workspace.repository');
        $changeset = $repo->diff($refLeft, $refRight);

        $mappedChanges = [];

        $apps = $this->container->get('veneer_core.workspace.app');


        foreach ($changeset as $path => $status) {
            $mappedChanges[$path] = [
                'path' => $path,
                'status' => $status,
            ];

            try {
                $mappedChanges[$path]['app'] = $apps->findApp($path);
            } catch (\Exception $e) {
                // oh well
            }

            if (!empty($mappedChanges[$path]['app'])) {
                if ($mappedChanges[$path]['app'] instanceof LifecycleInterface) {
                    $compiled = $mappedChanges[$path]['app']->onCompile($repo->createCheckout($changeset->getNewRef()), $path);

                    $logger = new Logger($path);

                    $mappedChanges[$path]['app']->onApply(
                        $logger,
                        $compiled
                    );

                    $mappedChanges[$path]['apply'] = $logger;
                }
            }
        }

        return $this->renderApi(
            'VeneerCoreBundle:WorkspaceRepoApply:index.html.twig',
            [
                'ref' => $ref,
                'changeset' => $changeset,
                'mapped_changes' => $mappedChanges,
            ],
            [
                'def_nav' => WorkspaceRepoController::defNav($this->container->get('veneer_core.breadcrumbs'), $ref, '/'),
            ]
        );
    }
}
