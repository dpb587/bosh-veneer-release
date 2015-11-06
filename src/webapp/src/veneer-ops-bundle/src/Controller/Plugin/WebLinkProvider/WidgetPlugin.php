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
            case 'veneer_bosh_deployment_job_summary':
                $deployment = $this->em->find('VeneerOpsBundle:DeploymentWorkspace', $_bosh['deployment']['name']);

                if (!$deployment) {
                    break;
                }

                return [
                    (new Link('ops_edit'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Edit Job')
                        ->setRoute(
                            'veneer_ops_workspace_app_deployment_edit',
                            [
                                'path' => $deployment->getSourcePath(),
                                'property' => 'jobs[' . $_bosh['job']['job'] . ']',
                            ]
                        ),
                    (new Link('ops_restart'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Restart')
                        ->setRoute(
                            'veneer_ops_deployment_job_restart',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'job' => $_bosh['job']['job'],
                            ]
                        ),
                    (new Link('ops_recreate'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Recreate')
                        ->setRoute(
                            'veneer_ops_deployment_job_recreate',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'job' => $_bosh['job']['job'],
                            ]
                        ),
                ];
            case 'veneer_bosh_deployment_job_index_summary':
                $deployment = $this->em->find('VeneerOpsBundle:DeploymentWorkspace', $_bosh['deployment']['name']);

                if (!$deployment) {
                    break;
                }

                return [
                    (new Link('ops_restart'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Restart')
                        ->setRoute(
                            'veneer_ops_deployment_job_index_restart',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'job' => $_bosh['job']['job'],
                                'index' => $_bosh['index']['index'],
                            ]
                        ),
                    (new Link('ops_recreate'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Recreate')
                        ->setRoute(
                            'veneer_ops_deployment_job_index_recreate',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'job' => $_bosh['job']['job'],
                                'index' => $_bosh['index']['index'],
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
