<?php

namespace Veneer\BoshBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\BoshBundle\Plugin\RequestContext\Annotations as BoshContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Veneer\BoshBundle\Form\Type\JobRecreateType;
use Veneer\BoshBundle\Form\Type\JobRestartType;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

/**
 * @BoshContext\DeploymentInstanceGroup
 */
class DeploymentInstanceGroupController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return DeploymentInstanceGroupALLController::defNav($nav, $_bosh)
            ->add(
                $_bosh['instance_group']['job'],
                [
                    'veneer_bosh_deployment_instancegroup_summary' => [
                        'deployment' => $_bosh['deployment']['name'],
                        'instance_group' => $_bosh['instance_group']['job'],
                    ],
                ],
                [
                    'expanded' => true,
                ]
            )
        ;
    }

    public function summaryAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstanceGroup:summary.html.twig',
            [
                'data' => $_bosh['instance_group'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }

    public function restartAction(Request $request, Context $_bosh)
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

            $task = $this->container->get('veneer_bosh.api')->sendForTaskId(
                new GuzzleRequest(
                    'PUT',
                    sprintf(
                        '/deployments/%s/jobs/%s?%s',
                        $_bosh['deployment']['name'],
                        $_bosh['instance_group']['job'],
                        http_build_query([
                            'state' => 'restart',
                            'skip_drain' => $payload['skip_drain'] ? 'true' : 'false',
                        ])
                    ),
                    [
                        'content-type' => 'text/yaml',
                    ]
                )
            );

            return $this->redirectToRoute(
                'veneer_bosh_task_summary',
                [
                    'task' => $task,
                    'continue' => $this->container->get('router')->generate(
                        'veneer_bosh_deployment_instancegroup_summary',
                        [
                            'deployment' => $_bosh['deployment']['name'],
                            'instance_group' => $_bosh['instance_group']['job'],
                        ]
                    ),
                ]
            );
        }

        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstanceGroup:restart.html.twig',
            [
                'form' => $form->createView(),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh)
                    ->add(
                        'Restart',
                        [
                            'veneer_bosh_deployment_instancegroup_restart' => [
                                'deployment' => $_bosh['deployment']['name'],
                                'instance_group' => $_bosh['instance_group']['job'],
                            ],
                        ]
                    ),
            ]
        );
    }

    public function recreateAction(Request $request, Context $_bosh)
    {
        $form = $this->container->get('form.factory')->createNamed(
            null,
            new JobRecreateType(),
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

            $task = $this->container->get('veneer_bosh.api')->sendForTaskId(
                new GuzzleRequest(
                    'PUT',
                    sprintf(
                        '/deployments/%s/jobs/%s?%s',
                        $_bosh['deployment']['name'],
                        $_bosh['instance_group']['job'],
                        http_build_query([
                            'state' => 'recreate',
                            'skip_drain' => $payload['skip_drain'] ? 'true' : 'false',
                        ])
                    ),
                    [
                        'content-type' => 'text/yaml',
                    ]
                )
            );

            return $this->redirectToRoute(
                'veneer_bosh_task_summary',
                [
                    'task' => $task,
                    'continue' => $this->container->get('router')->generate(
                        'veneer_bosh_deployment_instancegroup_summary',
                        [
                            'deployment' => $_bosh['deployment']['name'],
                            'instance_group' => $_bosh['instance_group']['job'],
                        ]
                    ),
                ]
            );
        }

        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstanceGroup:recreate.html.twig',
            [
                'form' => $form->createView(),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh)
                    ->add(
                        'Recreate',
                        [
                            'veneer_bosh_deployment_instancegroup_recreate' => [
                                'deployment' => $_bosh['deployment']['name'],
                                'instance_group' => $_bosh['instance_group']['job'],
                            ],
                        ]
                    ),
            ]
        );
    }
}
