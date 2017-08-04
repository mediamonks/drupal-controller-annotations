<?php

namespace Drupal\controller_annotations\RouteModifier;

/**
 * @Annotation
 */
class RouteMethod extends RouteMethodsBase
{

    /**
     * @param array $values
     *
     * @throws \RuntimeException
     */
    public function __construct(array $values)
    {
        if (!isset($values['value'])) {
            throw new \RuntimeException('Route method(s) must be specified.');
        }

        $value = $values['value'];

        if (is_string($value)) {
            $methods = [$value];
        }
        elseif (is_array($value)) {
            $methods = $value;
        }
        else {
            throw new \RuntimeException('Route methods must be string or array.');
        }

        parent::__construct($methods);
    }
}
