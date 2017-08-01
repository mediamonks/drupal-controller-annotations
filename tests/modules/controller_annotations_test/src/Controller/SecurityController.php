<?php

namespace Drupal\controller_annotations_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\controller_annotations\Configuration\Route;
use Drupal\controller_annotations\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("test/security/")
 */
class SecurityController extends ControllerBase
{
    /**
     * @Route("access")
     * @Security(access=true)
     */
    public function accessAction()
    {
        return new Response('OK');
    }

    /**
     * @Route("permission")
     * @Security(permission="access content")
     */
    public function permissionAction()
    {
        return new Response('OK');
    }

    /**
     * @Route("role")
     * @Security(role="administrator")
     */
    public function roleAction()
    {
        return new Response('OK');
    }
}
