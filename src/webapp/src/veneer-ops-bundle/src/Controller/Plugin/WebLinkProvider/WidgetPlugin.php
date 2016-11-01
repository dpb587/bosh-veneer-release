<?php

namespace Veneer\OpsBundle\Controller\Plugin\WebLinkProvider;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Plugin\LinkProvider\PluginInterface;
use Veneer\CoreBundle\Plugin\LinkProvider\Link;

class WidgetPlugin implements PluginInterface
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getLinks(Request $request, $route)
    {
        $_bosh = $request->attributes->get('_bosh');

        switch ($route) {
            case 'veneer_bosh_deployment_instancegroup_summary':
                $deployment = $this->em->find('VeneerOpsBundle:DeploymentWorkspace', $_bosh['deployment']['name']);

                if (!$deployment) {
                    break;
                }

                return [
                    (new Link('ops_edit'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Edit Instance Group')
                        ->setRoute(
                            'veneer_ops_workspace_app_deployment_edit',
                            [
                                'path' => $deployment->getSourcePath(),
                                'property' => 'instance_groups[' . $_bosh['instance_group']['job'] . ']',
                            ]
                        ),
                ];
            case 'veneer_bosh_deployment_summary':
                $deployment = $this->em->find('VeneerOpsBundle:DeploymentWorkspace', $_bosh['deployment']['name']);

                if (!$deployment) {
                    break;
                }

                return [
                    (new Link('ops_edit'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Edit Deployment')
                        ->setRoute(
                            'veneer_core_workspace_repo_app',
                            [
                                'path' => $deployment->getSourcePath(),
                            ]
                        ),
                ];
        }

        return [];
    }
}
