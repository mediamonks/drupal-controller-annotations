<?php

namespace Drupal\controller_annotations\RouteModifier;

/**
 * @Annotation
 */
class RouteOptions extends RouteOptionsBase
{

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        parent::__construct($values);
    }
}
