<?php

namespace Drupal\controller_annotations_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\controller_annotations\Configuration\Route;
use Drupal\controller_annotations\Configuration\Security;
use Drupal\controller_annotations\Configuration\Template;

/**
 * @Route("test/template/")
 */
class TemplateController extends ControllerBase
{
    /**
     * @Route("empty")
     * @Security(access=true)
     * @Template
     */
    public function emptyAction()
    {
    }

    /**
     * @Route("module-controller")
     * @Security(access=true)
     * @Template("controller_annotations_test:template")
     */
    public function moduleControllerAction()
    {
    }

    /**
     * @Route("module-controller-action")
     * @Security(access=true)
     * @Template("controller_annotations_test:template:action")
     */
    public function moduleControllerActionAction()
    {
    }

    /**
     * @Route("parameter")
     * @Security(access=true)
     * @Template
     */
    public function parameterAction()
    {
        return ['parameter' => 'value'];
    }

    /**
     * @Route("parameter-url/{parameter}")
     * @Security(access=true)
     * @Template
     */
    public function parameterUrlAction($parameter, $default = 'default')
    {
    }

    /**
     * @Route("streamable")
     * @Security(access=true)
     * @Template(isStreamable=true)
     */
    public function streamableAction()
    {
    }

    /**
     * @Route("vars/{name}")
     * @Security(access=true)
     * @Template()
     */
    public function varsAction($name = 'World')
    {
    }
}
