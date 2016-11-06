<?php

namespace Veneer\CoreBundle\Controller;

use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\CoreBundle\Service\Workspace\Lifecycle\LifecycleInterface;

class WorkspaceRepoReviewController extends AbstractController
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

                    $plan = $mappedChanges[$path]['app']->onPlan(
                        $repo->createCheckout($changeset->getOldRef()),
                        $repo->createCheckout($changeset->getNewRef()),
                        $path,
                        $compiled
                    );

                    $mappedChanges[$path]['plan'] = $plan;
                }
            }
        }

        return $this->renderApi(
            'VeneerCoreBundle:WorkspaceRepoReview:index.html.twig',
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
