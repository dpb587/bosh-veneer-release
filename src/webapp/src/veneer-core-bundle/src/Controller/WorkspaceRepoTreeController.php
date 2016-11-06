<?php

namespace Veneer\CoreBundle\Controller;

use Veneer\CoreBundle\Service\Breadcrumbs;

class WorkspaceRepoTreeController extends AbstractController
{
    public function indexAction($ref, $path)
    {
        $path = ltrim($path, '/');
        $repo = $this->container->get('veneer_core.workspace.repository');
        $checkout = $repo->createCheckout($ref);

        $ls = $checkout->ls($path);

        if ('master' == $ref) {
            // apps are based off master
            $apps = $this->container->get('veneer_core.workspace.app');

            foreach ($ls as $i => $item) {
                try {
                    $ls[$i]['app'] = $apps->findApp($path.'/'.$item['name']);
                } catch (\Exception $e) {
                }
            }
        }

        usort(
            $ls,
            function (array $a, array $b) {
                if (('dir' == $a['type']) xor ('dir' == $b['type'])) {
                    if ('dir' == $a['type']) {
                        return -1;
                    }

                    return 1;
                }

                return strcmp($a['name'], $b['name']);
            }
        );

        return $this->renderApi(
            'VeneerCoreBundle:WorkspaceRepoTree:index.html.twig',
            [
                'ref' => $ref,
                'path' => $path,
                'ls' => $ls,
            ],
            [
                'def_nav' => WorkspaceRepoController::defNav($this->container->get('veneer_core.breadcrumbs'), $ref, $path),
            ]
        );
    }
}
