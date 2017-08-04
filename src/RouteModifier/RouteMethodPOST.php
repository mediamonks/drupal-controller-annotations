<?php

namespace Drupal\controller_annotations\RouteModifier;

/**
 * @Annotation
 */
class RouteMethodPOST extends RouteMethodsBase
{

    public function __construct()
    {
        parent::__construct(['POST']);
    }
}
