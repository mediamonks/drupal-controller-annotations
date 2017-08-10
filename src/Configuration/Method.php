<?php

namespace Drupal\controller_annotations\Configuration;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method as BaseMethod;
use Symfony\Component\Routing\Route as RoutingRoute;

/**
 * @Annotation
 */
class Method extends BaseMethod implements RouteModifierMethodInterface, RouteModifierClassInterface
{
    /**
     * @param RoutingRoute $route
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     */
    public function modifyRouteClass(RoutingRoute $route, \ReflectionClass $class, \ReflectionMethod $method)
    {
        $this->modifyRoute($route);
    }

    /**
     * @param RoutingRoute $route
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     */
    public function modifyRouteMethod(RoutingRoute $route, \ReflectionClass $class, \ReflectionMethod $method)
    {
        $this->modifyRoute($route);
    }

    /**
     * we need to make sure this is an array instead of a string which is different in Symfony Framework
     * otherwise the support for defining an array of methods will not work as expected
     *
     * @param RoutingRoute $route
     */
    protected function modifyRoute(RoutingRoute $route)
    {
        $route->setMethods($this->getMethods());
    }
}
