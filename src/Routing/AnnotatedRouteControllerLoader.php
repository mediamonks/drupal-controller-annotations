<?php

namespace Drupal\controller_annotations\Routing;

use Drupal\controller_annotations\Configuration\Method;
use Drupal\controller_annotations\Configuration\Security;
use Drupal\controller_annotations\Configuration\Route as RouteConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Routing\AnnotatedRouteControllerLoader as BaseAnnotatedRouteControllerLoader;
use Symfony\Component\Routing\Route;

class AnnotatedRouteControllerLoader extends BaseAnnotatedRouteControllerLoader
{
    /**
     * @param Route $route
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     * @param mixed $annot
     */
    protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, $annot)
    {
        $this->setController($route, $class, $method);

        foreach ($this->reader->getMethodAnnotations($method) as $configuration) {
            if ($configuration instanceof RouteConfiguration) {
                $this->setRouteConfiguration($route, $configuration);
            }
            elseif ($configuration instanceof Method) {
                $this->setMethodConfiguration($route, $configuration);
            } elseif ($configuration instanceof Security) {
                $this->setSecurityConfiguration($route, $configuration);
            } elseif ($configuration instanceof RouteConfiguration && $configuration->getService()) {
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
        $classAnnot = $this->reader->getClassAnnotation($class, $this->routeAnnotationClass);
        if ($classAnnot instanceof RouteConfiguration && $service = $classAnnot->getService()) {
            $route->setDefault('_controller', $service.':'.$method->getName());
        } else {
            $route->setDefault('_controller', $class->getName().'::'.$method->getName());
        }
    }

    /**
     * @param Route $route
     * @param RouteConfiguration $routeConfiguration
     */
    private function setRouteConfiguration(Route $route, RouteConfiguration $routeConfiguration)
    {
        if ($routeConfiguration->isAdmin()) {
            $route->setOption('_admin_route', true);
        }
    }

    /**
     * we need to make sure this is an array instead of a string which is different in Symfony Framework
     * otherwise the support for defining an array of methods will not work as expected
     *
     * @param Route $route
     * @param Method $method
     */
    private function setMethodConfiguration(Route $route, Method $method)
    {
        $route->setMethods($method->getMethods());
    }

    /**
     * @param Route $route
     * @param Security $security
     */
    private function setSecurityConfiguration(Route $route, Security $security)
    {
        if ($security->isAccess()) {
            $route->setRequirement('_access', 'TRUE');
        }
        if ($security->hasPermission()) {
            $route->setRequirement('_permission', $security->getPermission());
        }
        if ($security->hasRole()) {
            $route->setRequirement('_role', $security->getRole());
        }
        if ($security->hasEntity()) {
            $route->setRequirement('_entity_access', $security->getEntity());
        }
        if ($security->hasCsrf()) {
            $route->setRequirement('_csrf_token', 'TRUE');
        }
        if ($security->hasCustom()) {
            $route->setRequirement('_custom_access', $security->getCustom());
        }
    }
}
