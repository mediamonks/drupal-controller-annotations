<?php

namespace Drupal\controller_annotations_test\Controller;

use Drupal\controller_annotations\Configuration\Route;
use Drupal\controller_annotations\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("test/invoke", service="controller.invoke")
 * @Security(access=true)
 */
class InvokeController
{
    public function __invoke()
    {
        return new Response('InvokeController::__invoke');
    }
}
