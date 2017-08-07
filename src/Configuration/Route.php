<?php

namespace Drupal\controller_annotations\Configuration;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route as BaseRoute;
use Symfony\Component\Routing\Route as RoutingRoute;

/**
 * @Annotation
 */
class Route extends BaseRoute implements RouteModifierInterface
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
     */
    public function modifyRoute(RoutingRoute $route)
    {
        if ($this->isAdmin()) {
            $route->setOption('_admin_route', true);
        }
    }
}
