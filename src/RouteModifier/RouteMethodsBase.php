<?php

namespace Drupal\controller_annotations\RouteModifier;

use Symfony\Component\Routing\Route;

abstract class RouteMethodsBase implements RouteModifierInterface
{

    /**
     * @var string[]
     */
    private $methods;

    /**
     * @param string[] $methods
     *   E.g. ['GET', 'POST'].
     */
    protected function __construct(array $methods)
    {
        $this->methods = $methods;
    }

    /**
     * @param \Symfony\Component\Routing\Route $route
     */
    public function modifyRoute(Route $route)
    {
        $route->setMethods($this->methods);
    }
}
