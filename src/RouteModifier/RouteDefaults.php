<?php

namespace Drupal\controller_annotations\RouteModifier;

/**
 * @Annotation
 */
class RouteDefaults extends RouteDefaultsBase
{

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        parent::__construct($values);
    }
}
