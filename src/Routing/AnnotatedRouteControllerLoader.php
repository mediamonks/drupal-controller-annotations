<?php

namespace Drupal\controller_annotations\Routing;

use Drupal\controller_annotations\Configuration\Method;
use Drupal\controller_annotations\Configuration\RouteModifierClassInterface;
use Drupal\controller_annotations\Configuration\RouteModifierMethodInterface;
use Symfony\Component\Routing\Loader\AnnotationClassLoader;
use Symfony\Component\Routing\Route;

class AnnotatedRouteControllerLoader extends AnnotationClassLoader
{
    /**
     * @param Route $route
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     * @param mixed $annotation
     */
    protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, $annotation)
    {
        $this->setController($route, $class, $method);
        $this->configureClassAnnotations($route, $class, $method);
        $this->configureMethodAnnotations($route, $class, $method);
    }

    /**
     * @param \ReflectionClass $class
     * @return array
     */
    protected function getGlobals(\ReflectionClass $class): array
    {
        $globals = parent::getGlobals($class);

        foreach ($this->reader->getClassAnnotations($class) as $configuration) {
            if ($configuration instanceof Method) {
                $globals['methods'] = array_merge($globals['methods'], $configuration->getMethods());
            }
        }

        return $globals;
    }

    /**
     * @param Route $route
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     */
    protected function configureClassAnnotations(Route $route, \ReflectionClass $class, \ReflectionMethod $method)
    {
        foreach ($this->reader->getClassAnnotations($class) as $configuration) {
            if ($configuration instanceof RouteModifierClassInterface) {
                $configuration->modifyRouteClass($route, $class, $method);
            }
        }
    }

    /**
     * @param Route $route
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     */
    protected function configureMethodAnnotations(Route $route, \ReflectionClass $class, \ReflectionMethod $method)
    {
        foreach ($this->reader->getMethodAnnotations($method) as $configuration) {
            if ($configuration instanceof RouteModifierMethodInterface) {
                $configuration->modifyRouteMethod($route, $class, $method);
            }
        }
    }

    /**
     * @param Route $route
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     */
    protected function setController(Route $route, \ReflectionClass $class, \ReflectionMethod $method)
    {
        $route->setDefault('_controller', $this->getControllerName($class, $method));
    }

    /**
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     * @return string
     */
    protected function getControllerName(\ReflectionClass $class, \ReflectionMethod $method)
    {
        $annotation = $this->reader->getClassAnnotation($class, $this->routeAnnotationClass);
        if ($annotation instanceof \Drupal\controller_annotations\Configuration\Route && $service = $annotation->getService(
            )) {
            return sprintf('%s:%s', $service, $method->getName());
        }

        return sprintf('%s::%s', $class->getName(), $method->getName());
    }
}
