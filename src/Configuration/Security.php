<?php

namespace Drupal\controller_annotations\Configuration;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * @Annotation
 */
class Security extends ConfigurationAnnotation
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
    protected $auth;

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
    public function hasAuth()
    {
        return !empty($this->auth);
    }

    /**
     * @return string
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * @param string $auth
     * @return Security
     */
    public function setAuth($auth)
    {
        $this->auth = $auth;

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
}
