<?php

namespace Drupal\controller_annotations\Configuration;

use Symfony\Component\Routing\Route;

interface RouteModifierInterface
{
    /**
     * @param Route $route
     */
    public function modifyRoute(Route $route);
}
