<?php

namespace Drupal\controller_annotations\RouteModifier;

/**
 * @Annotation
 */
class RouteRequirePermission extends RouteRequirementsBase
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

        $permissionName = $values['value'];

        if (!is_string($permissionName)) {
            throw new \RuntimeException('Permission name must be a string.');
        }

        parent::__construct(['_permission' => $permissionName]);
    }

}
