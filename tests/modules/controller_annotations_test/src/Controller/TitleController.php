<?php

namespace Drupal\controller_annotations_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\controller_annotations\Configuration\Route;
use Drupal\controller_annotations\Configuration\Security;
use Drupal\controller_annotations\Configuration\Title;

/**
 * @Route("test/title/")
 */
class TitleController extends ControllerBase
{

    /**
     * @Route("normal")
     * @Security(access=true)
     * @Title("Hello World")
     */
    public function normalAction()
    {
        return [];
    }

    /**
     * @Route("arguments")
     * @Security(access=true)
     * @Title("Hello @name", arguments={"@name":"MediaMonks"})
     */
    public function argumentsAction()
    {
        return [];
    }

    /**
     * @Route("callback")
     * @Security(access=true)
     * @Title(callback="\Drupal\controller_annotations_test\Title\Custom::title")
     */
    public function callbackAction()
    {
        return [];
    }

    /**
     * @Route("callback-inline")
     * @Security(access=true)
     * @Title(callback="title")
     */
    public function callbackInlineAction()
    {
        return [];
    }

    /**
     * @return string
     */
    public function title()
    {
        return 'Hello Callback Inline';
    }
}
