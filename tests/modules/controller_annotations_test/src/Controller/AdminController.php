<?php

namespace Drupal\controller_annotations_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\controller_annotations\Configuration\Route;
use Drupal\controller_annotations\Configuration\Security;

/**
 * @Route("test/admin/")
 */
class AdminController extends ControllerBase
{
    /**
     * @Route("admin", admin=true)
     * @Security(role="administrator")
     */
    public function adminAction()
    {
        return [];
    }

    /**
     * @Route("normal")
     * @Security(role="administrator")
     */
    public function normalAction()
    {
        return [];
    }
}
