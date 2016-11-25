<?php

namespace Veneer\BoshBundle\Controller;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Veneer\BoshBundle\Form\Type\JobRecreateType;
use Veneer\BoshBundle\Form\Type\JobRestartType;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Breadcrumbs;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Veneer\BoshBundle\Plugin\RequestContext\Annotations as BoshContext;

/**
 * @BoshContext\DeploymentInstanceGroupInstance
 */
class DeploymentInstanceGroupInstanceController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return DeploymentInstanceGroupInstanceALLController::defNav($nav, $_bosh)
            ->add(
                $_bosh['instance']['uuid'],
                [
                    'veneer_bosh_deployment_instancegroup_instance_summary' => [
                        'deployment' => $_bosh['deployment']['name'],
                        'instance_group' => $_bosh['instance_group']['job'],
                        'instance' => $_bosh['instance']['uuid'],
                    ],
                ]
            )
        ;
    }

    public function summaryAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstanceGroupInstance:summary.html.twig',
            [
                'data' => $_bosh['instance'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }

    public function specAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstanceGroupInstance:spec.html.twig',
            $_bosh['instance']['specJsonAsArray'],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }

    public function packagesAction(Context $_bosh)
    {
        $results = $_bosh['instance']['specJsonAsArray']['packages'];

        usort(
            $results,
            function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            }
        );

        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstanceGroupInstance:packages.html.twig',
            [
                'results' => $results,
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }

    public function templatesAction(Context $_bosh)
    {
        $results = $_bosh['instance']['specJsonAsArray']['job']['templates'];

        usort(
            $results,
            function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            }
        );

        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstanceGroupInstance:templates.html.twig',
            [
                'results' => $results,
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
                        '/deployments/%s/jobs/%s%s?%s',
                        $_bosh['deployment']['name'],
                        $_bosh['instance_group']['job'],
                        isset($_bosh['instance']) ? ('/'.$_bosh['instance']['uuid']) : '',
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
                ]
            );
        }

        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstanceGroupInstance:restart.html.twig',
            [
                'form' => $form->createView(),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh)
                    ->add(
                        'Restart',
                        [
                            'veneer_bosh_deployment_instancegroup_instance_restart' => [
                                'deployment' => $_bosh['deployment']['name'],
                                'instance_group' => $_bosh['instance_group']['job'],
                                'instance' => $_bosh['instance']['uuid'],
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
                        '/deployments/%s/jobs/%s%s?%s',
                        $_bosh['deployment']['name'],
                        $_bosh['instance_group']['job'],
                        isset($_bosh['instance']) ? ('/'.$_bosh['instance']['uuid']) : '',
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
                ]
            );
        }

        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstanceGroupInstance:recreate.html.twig',
            [
                'form' => $form->createView(),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh)
                    ->add(
                        'Recreate',
                        [
                            'veneer_bosh_deployment_instancegroup_instance_recreate' => [
                                'deployment' => $_bosh['deployment']['name'],
                                'instance_group' => $_bosh['instance_group']['job'],
                                'instance' => $_bosh['instance']['uuid'],
                            ],
                        ]
                    ),
            ]
        );
    }
}
