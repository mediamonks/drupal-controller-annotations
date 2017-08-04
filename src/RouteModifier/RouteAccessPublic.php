<?php

namespace Drupal\controller_annotations\RouteModifier;

/**
 * @Annotation
 */
class RouteAccessPublic extends RouteRequirementsBase
{

    public function __construct()
    {
        parent::__construct(['_access' => 'TRUE']);
    }

}
