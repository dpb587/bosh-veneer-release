<?php

namespace Veneer\CoreBundle\Controller;

use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\CoreBundle\Service\Storage\Object\AbstractObject;

class StorageTreeController extends AbstractController
{
    public function indexAction($ref, $path)
    {
        $storageSystem = $this->container->get('veneer_core.storage.system');

        $tree = $storageSystem->ls($path, [ 'ref' => $ref ]);
        $children = $tree->getChildren();

//        if ('master' == $ref) {
//            // apps are based off master
//            $apps = $this->container->get('veneer_core.workspace.app');
//
//            foreach ($ls as $i => $item) {
//                try {
//                    $ls[$i]['app'] = $apps->findApp($path.'/'.$item['name']);
//                } catch (\Exception $e) {
//                }
//            }
//        }

        usort(
            $children,
            function (AbstractObject $a, AbstractObject $b) {
                if ((AbstractObject::OBJECT_TYPE_DIR == $a->getObjectType()) xor (AbstractObject::OBJECT_TYPE_DIR == $b->getObjectType())) {
                    if (AbstractObject::OBJECT_TYPE_DIR == $a->getObjectType()) {
                        return -1;
                    }

                    return 1;
                }

                return strcmp($a->getBasename(), $b->getBasename());
            }
        );

        return $this->renderApi(
            'VeneerCoreBundle:StorageTree:index.html.twig',
            [
                'ref' => $ref,
                'path' => $path,
                'tree' => $tree,
                'children' => $children,
            ],
            [
                'def_nav' => StorageController::defNav($this->container->get('veneer_core.breadcrumbs'), $ref, $path),
            ]
        );
    }
}
