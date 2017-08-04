<?php

namespace Drupal\controller_annotations\RouteModifier;

/**
 * @Annotation
 */
class RouteTitleCallback extends RouteDefaultsBase
{

    /**
     * @param array $values
     *
     * @throws \RuntimeException
     */
    public function __construct(array $values)
    {
        if (!isset($values['value'])) {
            throw new \RuntimeException('Title callback must be specified.');
        }

        $titleCallback = $values['value'];

        if (!is_callable($titleCallback)) {
            throw new \RuntimeException('Title callback must be callable.');
        }

        parent::__construct(['_title_callback' => $titleCallback]);
    }
}
