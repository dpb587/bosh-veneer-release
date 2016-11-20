<?php

namespace Veneer\CoreBundle\Plugin\RequestContext;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Factory
{
    protected $container;
    protected $annotationReader;
    protected $contextMap;

    public function __construct(ContainerInterface $container, AnnotationReader $annotationReader, array $contextMap = [])
    {
        $this->container = $container;
        $this->annotationReader = $annotationReader;
        $this->contextMap = $contextMap;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        $request = $event->getRequest();
        $context = $request->attributes->get('_bosh') ?: new Context();

        list($controllerObject, $methodName) = $controller;

        $controllerClass = ClassUtils::getClass($controllerObject);

        $annotations = array_merge(
            array_filter(
                $this->annotationReader->getMethodAnnotations(new \ReflectionMethod($controllerClass, $methodName)),
                function ($annotation) {
                    return $annotation instanceof Annotation;
                }
            ),
            array_filter(
                $this->annotationReader->getClassAnnotations(new \ReflectionClass($controllerClass)),
                function ($annotation) {
                    return $annotation instanceof Annotation;
                }
            )
        );

        $callback = false;

        foreach ($annotations as $annotation) {
            if ($annotation instanceof Annotations\ControllerMethod) {
                // eww
                $callback = true;

                continue;
            }

            $annotationClass = ClassUtils::getClass($annotation);

            if (!isset($this->contextMap[$annotationClass])) {
                throw new \LogicException(sprintf('Annotation class is not registered: %s', $annotationClass));
            }

            $this->container->get($this->contextMap[$annotationClass])->apply($request, $annotation, $context);
        }

        if ($callback) {
            call_user_func([$controller[0], 'applyRequestContext'], $request, $context);
        }

        $request->attributes->set('_bosh', $context);
    }
}
