<?php

namespace Drupal\controller_annotations\RouteModifier;

use Symfony\Component\Routing\Route;

abstract class RouteOptionsBase implements RouteModifierInterface
{

    /**
     * @var array
     */
    private $options;

    /**
     * @param array $options
     */
    protected function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param \Symfony\Component\Routing\Route $route
     */
    public function modifyRoute(Route $route)
    {
        $route->addOptions($this->options);
    }
}
