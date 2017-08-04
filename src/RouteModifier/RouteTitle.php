<?php

namespace Drupal\controller_annotations\RouteModifier;

/**
 * @Annotation
 */
class RouteTitle extends RouteDefaultsBase
{

    /**
     * @param array $values
     *
     * @throws \RuntimeException
     */
    public function __construct(array $values)
    {
        if (!isset($values['value'])) {
            throw new \RuntimeException('Permission name must be specified.');
        }

        $title = $values['value'];

        if (!is_string($title)) {
            throw new \RuntimeException('Title must be a string.');
        }

        parent::__construct(['_title' => $title]);
    }
}
