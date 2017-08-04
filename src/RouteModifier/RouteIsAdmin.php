<?php

namespace Drupal\controller_annotations\RouteModifier;

/**
 * @Annotation
 */
class RouteIsAdmin extends RouteOptionsBase
{

    public function __construct()
    {
        parent::__construct(['_admin_route' => true]);
    }

}
