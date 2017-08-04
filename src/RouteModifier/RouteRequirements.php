<?php

namespace Drupal\controller_annotations\RouteModifier;

/**
 * @Annotation
 */
class RouteRequirements extends RouteRequirementsBase
{
    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        parent::__construct($values);
    }
}
