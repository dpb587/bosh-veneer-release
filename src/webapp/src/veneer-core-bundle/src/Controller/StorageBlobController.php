<?php

namespace Veneer\CoreBundle\Controller;

use Veneer\CoreBundle\Service\Breadcrumbs;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StorageBlobController extends AbstractController
{
    public function indexAction($ref, $path)
    {
        $path = ltrim($path, '/');
        $storageSystem = $this->container->get('veneer_core.storage.system');

        $file = $storageSystem->get($path, [ 'ref' => $ref ]);

        $apps = $this->container->get('veneer_core.workspace.app');

        try {
            $app = $apps->findApp($file->getPath());
        } catch (\Exception $e) {
        }

        return $this->renderApi(
            'VeneerCoreBundle:StorageBlob:index.html.twig',
            [
                'ref' => $ref,
                'path' => $path,
                'data' => $file->getData(),
            ],
            [
                'def_nav' => StorageController::defNav($this->container->get('veneer_core.breadcrumbs'), $ref, $path),
                'workspace_app' => $app,
            ]
        );
    }
}
