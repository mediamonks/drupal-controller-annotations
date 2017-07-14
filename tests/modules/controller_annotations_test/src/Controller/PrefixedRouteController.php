<?php

namespace Drupal\controller_annotations_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\controller_annotations\Configuration\Route;
use Drupal\controller_annotations\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("test/prefix")
 */
class PrefixedRouteController extends ControllerBase
{
    /**
     * @Route
     * @Security(access=true)
     */
    public function emptyRouteAction()
    {
        return new Response('PrefixedBasicController::emptyRouteAction');
    }

    /**
     * @Route("/named", name="named_route")
     * @Security(access=true)
     */
    public function namedRouteAction()
    {
        return new Response('PrefixedBasicController::namedRouteAction');
    }
}
