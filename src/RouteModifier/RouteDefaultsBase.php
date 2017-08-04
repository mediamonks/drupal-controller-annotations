<?php

namespace Drupal\controller_annotations\RouteModifier;

use Symfony\Component\Routing\Route;

abstract class RouteDefaultsBase implements RouteModifierInterface
{

    /**
     * @var array
     */
    private $defaults;

    /**
     * @param array $defaults
     */
    protected function __construct(array $defaults)
    {
        $this->defaults = $defaults;
    }

    /**
     * @param \Symfony\Component\Routing\Route $route
     */
    public function modifyRoute(Route $route)
    {
        $route->addDefaults($this->defaults);
    }
}
