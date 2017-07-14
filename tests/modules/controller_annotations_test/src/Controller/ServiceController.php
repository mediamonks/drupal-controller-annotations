<?php

namespace Drupal\controller_annotations_test\Controller;

use Drupal\controller_annotations\Configuration\Route;
use Drupal\controller_annotations\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(service="controller.service")
 */
class ServiceController
{
    /**
     * @Route("test/service")
     * @Security(access=true)
     */
    public function getAction()
    {
        return new Response('ServiceController::getAction');
    }
}
