<?php

namespace Drupal\controller_annotations\RouteModifier;

use Symfony\Component\Routing\Route;

interface RouteModifierInterface
{

    /**
     * @param \Symfony\Component\Routing\Route $route
     */
    public function modifyRoute(Route $route);

}
