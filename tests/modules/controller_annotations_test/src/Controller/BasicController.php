<?php

namespace Drupal\controller_annotations_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\controller_annotations\Configuration\Route;
use Drupal\controller_annotations\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Security(access=true)
 */
class BasicController extends ControllerBase {

  /**
   * @Route("test/basic")
   */
  public function basicAction() {
    return new Response('BasicController::basicAction');
  }

}
