<?php

namespace Drupal\controller_annotations\RouteModifier\Annotated;

use Symfony\Component\Routing\Route;

/**
 * A special interface for annotations that need to know about the class and method that were annotated as a route.
 */
interface AnnotatedRouteModifierInterface
{

    /**
     * @param \Symfony\Component\Routing\Route $route
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     * @param mixed $annot
     */
    public function modifyAnnotatedRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, $annot);

}
