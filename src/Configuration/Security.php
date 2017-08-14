<?php

namespace Drupal\controller_annotations\Configuration;

use Symfony\Component\Routing\Route as RoutingRoute;

/**
 * @Annotation
 */
class Security extends ConfigurationAnnotation implements RouteModifierMethodInterface, RouteModifierClassInterface
{
    /**
     * @var string
     */
    protected $permission;

    /**
     * @var string
     */
    protected $role;

    /**
     * @var bool
     */
    protected $access;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var bool
     */
    protected $csrf;

    /**
     * @var string
     */
    protected $custom;

    /**
     * @return bool
     */
    public function hasPermission()
    {
        return !empty($this->permission);
    }

    /**
     * @return string
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @param string $permission
     * @return Security
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasRole()
    {
        return !empty($this->role);
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     * @return Security
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAccess()
    {
        return $this->access;
    }

    /**
     * @param bool $access
     * @return Security
     */
    public function setAccess($access)
    {
        $this->access = $access;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasEntity()
    {
        return !empty($this->entity);
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param string $entity
     * @return Security
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasCustom()
    {
        return !empty($this->custom);
    }

    /**
     * @return string
     */
    public function getCustom()
    {
        return $this->custom;
    }

    /**
     * @param string $custom
     * @return Security
     */
    public function setCustom($custom)
    {
        $this->custom = $custom;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasCsrf()
    {
        return !empty($this->csrf);
    }

    /**
     * @param bool $csrf
     * @return Security
     */
    public function setCsrf($csrf)
    {
        $this->csrf = $csrf;

        return $this;
    }

    public function getAliasName()
    {
        return 'security';
    }

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
        $this->modifyRoute($route, $class);
    }

    /**
     * @param RoutingRoute $route
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     */
    public function modifyRouteMethod(RoutingRoute $route, \ReflectionClass $class, \ReflectionMethod $method)
    {
        $this->modifyRoute($route, $class);
    }

    /**
     * @param RoutingRoute $route
     * @param \ReflectionClass $class
     */
    protected function modifyRoute(RoutingRoute $route, \ReflectionClass $class)
    {
        if ($this->isAccess()) {
            $route->setRequirement('_access', 'TRUE');
        }
        if ($this->hasPermission()) {
            $route->setRequirement('_permission', $this->getPermission());
        }
        if ($this->hasRole()) {
            $route->setRequirement('_role', $this->getRole());
        }
        if ($this->hasEntity()) {
            $route->setRequirement('_entity_access', $this->getEntity());
        }
        if ($this->hasCsrf()) {
            $route->setRequirement('_csrf_token', 'TRUE');
        }

        $this->setCustomSecurity($route, $class);
    }

    /**
     * @param RoutingRoute $route
     * @param \ReflectionClass $class
     */
    protected function setCustomSecurity(RoutingRoute $route, \ReflectionClass $class)
    {
        if ($this->hasCustom()) {
            if (strpos($this->getCustom(), '::') === false && $class->hasMethod($this->getCustom())) {
                $this->setCustom(sprintf('%s::%s', $class->getName(), $this->getCustom()));
            }
            $route->setRequirement('_custom_access', $this->getCustom());
        }
    }
}
