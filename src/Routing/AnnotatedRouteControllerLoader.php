<?php

namespace Drupal\controller_annotations\Routing;

use Drupal\controller_annotations\Configuration\RouteModifierInterface;
use Drupal\controller_annotations\Configuration\Route as RouteConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Routing\AnnotatedRouteControllerLoader as BaseAnnotatedRouteControllerLoader;
use Symfony\Component\Routing\Route;

class AnnotatedRouteControllerLoader extends BaseAnnotatedRouteControllerLoader
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
        $this->configureClassAnnotations($route, $class);
        $this->configureMethodAnnotations($route, $method);
    }

    /**
     * @param Route $route
     * @param \ReflectionClass $class
     */
    protected function configureClassAnnotations(Route $route, \ReflectionClass $class)
    {
        foreach ($this->reader->getClassAnnotations($class) as $configuration) {
            if ($configuration instanceof RouteModifierInterface) {
                $configuration->modifyRoute($route);
            }
        }
    }

    /**
     * @param Route $route
     * @param \ReflectionMethod $method
     */
    protected function configureMethodAnnotations(Route $route, \ReflectionMethod $method)
    {
        foreach ($this->reader->getMethodAnnotations($method) as $configuration) {
            if ($configuration instanceof RouteModifierInterface) {
                $configuration->modifyRoute($route);
            }

            if ($configuration instanceof RouteConfiguration && $configuration->getService()) {
                throw new \LogicException('The service option can only be specified at class level.');
            }
        }
    }

    /**
     * @param Route $route
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     */
    private function setController(Route $route, \ReflectionClass $class, \ReflectionMethod $method)
    {
        $classAnnotation = $this->reader->getClassAnnotation($class, $this->routeAnnotationClass);
        if ($classAnnotation instanceof RouteConfiguration && $service = $classAnnotation->getService()) {
            $route->setDefault('_controller', $service . ':' . $method->getName());
        } else {
            $route->setDefault('_controller', $class->getName() . '::' . $method->getName());
        }
    }
}
