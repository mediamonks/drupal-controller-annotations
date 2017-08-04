<?php

namespace Drupal\controller_annotations\RouteModifier;

/**
 * @Annotation
 */
class RouteMethodGET extends RouteMethodsBase
{

    public function __construct()
    {
        parent::__construct(['GET']);
    }
}
