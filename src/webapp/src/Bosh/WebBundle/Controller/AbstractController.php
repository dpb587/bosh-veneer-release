<?php

namespace Bosh\WebBundle\Controller;

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

    public function renderApi($view, array $data = [], array $nondata = [])
    {
        $request = $this->container->get('request');
        $_format = $request->attributes->get('_format', 'html');

        if ('json' == $_format) {
            return new JsonResponse($this->normalizeApiResult($data));
        } elseif ('html' == $_format) {
            $context = $request->attributes->get('_context', []);

            return $this->render(
                $view,
                array_merge(
                    [
                        '_uuid' => md5(microtime(true)),
                        '_user_links_primary' => $this->container->get('bosh_core.plugin_factory')->getUserPrimaryLinks(
                            $request->attributes->get('_bosh_web_object_context'),
                            $context
                        ),
                        '_bosh_core_context' => $context,
                    ],
                    $data,
                    $nondata
                )
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
            'default' => 'BoshWebBundle:Layout:default.html.twig',
            'fragment' => 'BoshWebBundle:Layout:fragment.html.twig',
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
