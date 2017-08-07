<?php

namespace Drupal\controller_annotations\Configuration;

use Symfony\Component\Routing\Route as RoutingRoute;

interface RouteModifierInterface
{
    /**
     * @param RoutingRoute $route
     */
    public function modifyRoute(RoutingRoute $route);
}
