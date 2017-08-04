<?php

namespace Drupal\controller_annotations\RouteModifier;

use Symfony\Component\Routing\Route;

abstract class RouteRequirementsBase implements RouteModifierInterface
{

    /**
     * @var array
     */
    private $requirements;

    /**
     * @param array $requirements
     */
    protected function __construct(array $requirements)
    {
        $this->requirements = $requirements;
    }

    /**
     * @param \Symfony\Component\Routing\Route $route
     */
    public function modifyRoute(Route $route)
    {
        $route->addRequirements($this->requirements);
    }
}
