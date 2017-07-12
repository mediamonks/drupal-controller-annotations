<?php

namespace Drupal\controller_annotations_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\controller_annotations\Configuration\Route;
use Drupal\controller_annotations\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

class BasicController extends ControllerBase
{
    /**
     * @Route("test/basic")
     * @Security(access=true)
     */
    public function basicAction()
    {
        return new Response('OK');
    }
}
