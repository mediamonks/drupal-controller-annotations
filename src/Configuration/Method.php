<?php

namespace Drupal\controller_annotations\Configuration;

use Symfony\Component\Routing\Route as RoutingRoute;

/**
 * @Annotation
 */
class Method extends ConfigurationAnnotation implements RouteModifierMethodInterface, RouteModifierClassInterface
{
    /**
     * An array of restricted HTTP methods.
     *
     * @var array
     */
    protected $methods = array();

    /**
     * Returns the array of HTTP methods.
     *
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Sets the HTTP methods.
     *
     * @param array|string $methods An HTTP method or an array of HTTP methods
     */
    public function setMethods($methods)
    {
        $this->methods = is_array($methods) ? $methods : array($methods);
    }

    /**
     * Sets the HTTP methods.
     *
     * @param array|string $methods An HTTP method or an array of HTTP methods
     */
    public function setValue($methods)
    {
        $this->setMethods($methods);
    }

    /**
     * Returns the annotation alias name.
     *
     * @return string
     *
     * @see ConfigurationInterface
     */
    public function getAliasName()
    {
        return 'method';
    }

    /**
     * Only one method directive is allowed.
     *
     * @return bool
     *
     * @see ConfigurationInterface
     */
    public function allowArray()
    {
        return false;
    }

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
