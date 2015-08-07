<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractController extends Controller
{
    protected function validateRequest(Request $request)
    {
        return [];
    }

    public function renderApi($view, array $context = [], array $params = [], array $links = [])
    {
        $request = $this->container->get('request');
        $_format = $request->attributes->get('_format', 'html');

        if ('json' == $_format) {
            return new JsonResponse([
                'data' => $this->normalizeApiResult($params),
                'links' => $links,
            ]);
        } elseif ('html' == $_format) {
            return $this->render(
                $view,
                [
                    'context' => $context,
                    'links' => $links,
                    'data' => $params,
                ]
            );
        }
    }
    
    protected function normalizeApiResult($result)
    {
        if (is_array($result)) {
            foreach ($result as &$v) {
                $v = $this->normalizeApiResult($v);
            }
        } elseif (is_object($result) && method_exists($result, 'toArray')) {
            return $this->normalizeApiResult($result->toArray());
        }

        return $result;
    }

    public function renderView($view, array $parameters = array())
    {
        return parent::renderView(
            $view,
            $this->getRenderParameters($parameters)
        );
    }

    public function render($view, array $parameters = array(), Response $response = null)
    {
        return $this->container->get('templating')->renderResponse(
            $view,
            $this->getRenderParameters($parameters),
            $response
        );
    }
    
    protected function getRenderParameters(array $parameters)
    {
        $request = $this->container->get('request');

        $layouts = [
            'default' => 'BoshCoreBundle:Common:layout.html.twig',
            'fragment' => 'BoshCoreBundle:Common:fragment.html.twig',
        ];

        return array_merge(
            [
                '_layout' => $request->query->has('_frag') ? $layouts['fragment'] : $layouts['default'],
                '_frag' => $request->query->get('_frag'),
            ],
            $parameters
        );
    }
}
