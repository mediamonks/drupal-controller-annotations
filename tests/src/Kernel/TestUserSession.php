<?php

namespace Drupal\Tests\controller_annotations\Kernel;

use Drupal\Core\Session\UserSession;

class TestUserSession extends UserSession
{
    /**
     * @var array
     */
    protected $permissions = [];

    /**
     * {@inheritdoc}
     */
    public function hasPermission($permission)
    {
        // User #1 has all privileges.
        if ((int) $this->id() === 1) {
            return true;
        }

        return in_array($permission, $this->permissions);
    }
}
