<?php

namespace Drupal\controller_annotations\Configuration;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route as BaseRoute;
use Symfony\Component\Routing\Route as RoutingRoute;

/**
 * @Annotation
 */
class Route extends BaseRoute implements RouteModifierMethodInterface, RouteModifierClassInterface
{
    /**
     * @var bool
     */
    protected $admin;

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->admin;
    }

    /**
     * @param bool $admin
     * @return Route
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * @param RoutingRoute $route
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     */
    public function modifyRouteClass(RoutingRoute $route, \ReflectionClass $class, \ReflectionMethod $method)
    {
        $this->modifyRoute($route, $class, $method);
    }

    /**
     * @param RoutingRoute $route
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     */
    public function modifyRouteMethod(RoutingRoute $route, \ReflectionClass $class, \ReflectionMethod $method)
    {
        if ($this->getService()) {
            throw new \LogicException('The service option can only be specified at class level.');
        }

        $this->modifyRoute($route, $class, $method);
    }

    /**
     * @param RoutingRoute $route
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     */
    protected function modifyRoute(RoutingRoute $route, \ReflectionClass $class, \ReflectionMethod $method)
    {
        if ($this->isAdmin()) {
            $route->setOption('_admin_route', true);
        }
    }
}
