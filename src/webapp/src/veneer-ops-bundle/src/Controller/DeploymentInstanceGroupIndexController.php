<?php

namespace Veneer\OpsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\OpsBundle\Form\Type\JobRestartType;
use Veneer\BoshBundle\Controller\DeploymentInstanceGroupIndexController as BoshDeploymentInstanceGroupIndexController;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

class DeploymentInstanceGroupIndexController extends AbstractController
{
    public function restartAction(Request $request, array $_bosh)
    {
        $form = $this->container->get('form.factory')->createNamed(
            null,
            new JobRestartType(),
            null,
            [
                'csrf_protection' => false,
            ]
        );

        $form->bind($request);

        if (Request::METHOD_POST == $request->getMethod()) {
            if (!$form->isValid()) {
                throw new HttpException(400);
            }

            $payload = $form->getData();

            $state = $this->container->get('doctrine.orm.state_entity_manager')
                ->getRepository('VeneerOpsBundle:DeploymentWorkspace')
                ->findOneBy([
                    'deployment' => $_bosh['deployment']['name'],
                ]);

            $task = $this->container->get('veneer_bosh.api')->sendForTaskId(
                new GuzzleRequest(
                    'PUT',
                    sprintf(
                        '/deployments/%s/jobs/%s%s?%s',
                        $_bosh['deployment']['name'],
                        $_bosh['job']['job'],
                        isset($_bosh['index']) ? ('/' . $_bosh['index']['index']) : '',
                        http_build_query([
                            'state' => 'restart',
                            'skip_drain' => $payload['skip_drain'] ? 'true' : 'false',
                        ])
                    ),
                    [
                        'content-type' => 'text/yaml',
                    ],
                    $this->container->get('veneer_core.workspace.repository')->showFile(
                        dirname($state->getSourcePath()) . '/.' . basename($state->getSourcePath()),
                        'master'
                    )
                )
            );

            return $this->redirectToRoute(
                'veneer_bosh_task_summary',
                [
                    'task' => $task,
                ]
            );
        }

        return $this->renderApi(
            'VeneerOpsBundle:DeploymentInstanceGroupIndex:restart.html.twig',
            [
                'form' => $form->createView(),
            ],
            [
                'def_nav' => BoshDeploymentInstanceGroupIndexController::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh)
                    ->add(
                        'Restart',
                        [
                            'veneer_ops_deployment_job_index_restart' => [
                                'deployment' => $_bosh['deployment']['name'],
                                'job' => $_bosh['job']['job'],
                                'index' => $_bosh['index']['index'],
                            ],
                        ]
                    ),
            ]
        );
    }

    public function recreateAction(Request $request, array $_bosh)
    {
        $form = $this->container->get('form.factory')->createNamed(
            null,
            new JobRestartType(),
            null,
            [
                'csrf_protection' => false,
            ]
        );

        $form->bind($request);

        if (Request::METHOD_POST == $request->getMethod()) {
            if (!$form->isValid()) {
                throw new HttpException(400);
            }

            $payload = $form->getData();

            $state = $this->container->get('doctrine.orm.state_entity_manager')
                ->getRepository('VeneerOpsBundle:DeploymentWorkspace')
                ->findOneBy([
                    'deployment' => $_bosh['deployment']['name'],
                ]);

            $task = $this->container->get('veneer_bosh.api')->sendForTaskId(
                new GuzzleRequest(
                    'PUT',
                    sprintf(
                        '/deployments/%s/jobs/%s%s?%s',
                        $_bosh['deployment']['name'],
                        $_bosh['job']['job'],
                        isset($_bosh['index']) ? ('/' . $_bosh['index']['index']) : '',
                        http_build_query([
                            'state' => 'recreate',
                            'skip_drain' => $payload['skip_drain'] ? 'true' : 'false',
                        ])
                    ),
                    [
                        'content-type' => 'text/yaml',
                    ],
                    $this->container->get('veneer_core.workspace.repository')->showFile(
                        dirname($state->getSourcePath()) . '/.' . basename($state->getSourcePath()),
                        'master'
                    )
                )
            );

            return $this->redirectToRoute(
                'veneer_bosh_task_summary',
                [
                    'task' => $task,
                ]
            );
        }

        return $this->renderApi(
            'VeneerOpsBundle:DeploymentInstanceGroupIndex:recreate.html.twig',
            [
                'form' => $form->createView(),
            ],
            [
                'def_nav' => BoshDeploymentInstanceGroupIndexController::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh)
                    ->add(
                        'Recreate',
                        [
                            'veneer_ops_deployment_job_index_recreate' => [
                                'deployment' => $_bosh['deployment']['name'],
                                'job' => $_bosh['job']['job'],
                                'index' => $_bosh['index']['index'],
                            ],
                        ]
                    ),
            ]
        );
    }
}
