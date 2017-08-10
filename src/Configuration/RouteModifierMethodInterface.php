<?php

namespace Drupal\controller_annotations\Configuration;

use Symfony\Component\Routing\Route as RoutingRoute;

interface RouteModifierMethodInterface extends RouteModifierInterface
{
    /**
     * @param RoutingRoute $route
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     */
    public function modifyRouteMethod(RoutingRoute $route, \ReflectionClass $class, \ReflectionMethod $method);
}
