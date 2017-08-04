<?php

namespace Drupal\controller_annotations_test\Controller;

use Drupal\Component\Utility\Html;
use Drupal\controller_annotations\Configuration\Route;
use Drupal\controller_annotations\RouteModifier\Annotated\RouteTitleMethod;
use Drupal\controller_annotations\RouteModifier\RouteAccessPublic;
use Drupal\controller_annotations\RouteModifier\RouteDefaults;
use Drupal\controller_annotations\RouteModifier\RouteIsAdmin;
use Drupal\controller_annotations\RouteModifier\RouteMethod;
use Drupal\controller_annotations\RouteModifier\RouteMethodGET;
use Drupal\controller_annotations\RouteModifier\RouteMethodPOST;
use Drupal\controller_annotations\RouteModifier\RouteOptions;
use Drupal\controller_annotations\RouteModifier\RouteRequirements;
use Drupal\controller_annotations\RouteModifier\RouteRequirePermission;
use Drupal\controller_annotations\RouteModifier\RouteTitle;
use Drupal\controller_annotations\RouteModifier\RouteTitleCallback;

/**
 * @Route("test/modifier/")
 */
class RouteModifierController
{
    /**
     * @param string $arg
     *
     * @return string
     */
    public function title($arg) {
        // @todo Is it necessary to return sanitized HTML?
        return Html::escape($arg);
    }

    /**
     * @Route("accessPublic")
     * @RouteAccessPublic
     *
     * @return array
     */
    public function accessPublic() {
        return ['#markup' => 'OK'];
    }

    /**
     * @Route("defaultXY")
     * @RouteDefaults(_x = "Y")
     *
     * @return array
     */
    public function defaultXY() {
        return ['#markup' => 'OK'];
    }

    /**
     * @Route("admin")
     * @RouteIsAdmin
     *
     * @return array
     */
    public function admin() {
        return ['#markup' => 'OK'];
    }

    /**
     * @Route("methodString")
     * @RouteMethod("GET")
     *
     * @return array
     */
    public function methodString() {
        return ['#markup' => 'OK'];
    }

    /**
     * @Route("methodArray")
     * @RouteMethod({"GET", "POST"})
     *
     * @return array
     */
    public function methodArray() {
        return ['#markup' => 'OK'];
    }

    /**
     * @Route("methodGET")
     * @RouteMethodGET
     *
     * @return array
     */
    public function methodGET() {
        return ['#markup' => 'OK'];
    }

    /**
     * @Route("methodPOST")
     * @RouteMethodPOST
     *
     * @return array
     */
    public function methodPOST() {
        return ['#markup' => 'OK'];
    }

    /**
     * @Route("optionsXY")
     * @RouteOptions(_x = "Y")
     *
     * @return array
     */
    public function optionsXY() {
        return ['#markup' => 'OK'];
    }

    /**
     * @Route("requirements")
     * @RouteRequirements(_permission = "administer site configuration")
     *
     * @return array
     */
    public function requirements() {
        return ['#markup' => 'OK'];
    }

    /**
     * @Route("permission")
     * @RouteRequirePermission("administer site configuration")
     *
     * @return array
     */
    public function permission() {
        return ['#markup' => 'OK'];
    }

    /**
     * @Route("pageWithTitle")
     * @RouteTitle("Hello")
     *
     * @return array
     */
    public function pageWithTitle() {
        return ['#markup' => 'OK'];
    }

    /**
     * @Route("pageWithTitleCallback/{arg}")
     * @RouteTitleCallback("\Drupal\controller_annotations_test\Controller\RouteModifierController::title")
     *
     * @return array
     */
    public function pageWithTitleCallback($arg) {
        return ['#markup' => Html::escape($arg)];
    }

    /**
     * @Route("pageWithTitleMethod/{arg}")
     * @RouteTitleMethod("title")
     *
     * @return array
     */
    public function pageWithTitleMethod($arg) {
        return ['#markup' => Html::escape($arg)];
    }
}
