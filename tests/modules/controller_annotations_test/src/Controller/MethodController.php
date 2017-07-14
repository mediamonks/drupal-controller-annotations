<?php

namespace Drupal\controller_annotations_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\controller_annotations\Configuration\Route;
use Drupal\controller_annotations\Configuration\Security;
use Drupal\controller_annotations\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

class MethodController extends ControllerBase
{
    /**
     * @Route("test/method")
     * @Method("GET")
     * @Security(access=true)
     */
    public function getAction()
    {
        return new Response('ClassRouteController::getAction');
    }

    /**
     * @Route("test/method")
     * @Method("POST")
     * @Security(access=true)
     */
    public function postAction()
    {
        return new Response('ClassRouteController::postAction');
    }

    /**
     * @Route("test/method/multiple")
     * @Method({"GET", "POST"})
     * @Security(access=true)
     */
    public function getAndPostAction()
    {
        return new Response('ClassRouteController::getAndPostAction');
    }
}
