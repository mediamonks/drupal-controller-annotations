<?php

namespace Drupal\controller_annotations\RouteModifier;

/**
 * @Annotation
 */
class RouteParameters extends RouteOptionsBase {

  /**
   * @param array $values
   */
  public function __construct(array $values) {

    $parameters = [];

    foreach ($values as $k => $v) {
      if (is_string($v)) {
        $parameters[$k] = ['type' => $v];
      }
      elseif ([] === $v) {
        // @todo Is this allowed?
        $parameters[$k] = [];
      }
      elseif (is_array($v)) {
        if ('type' !== array_keys($v)) {
          throw new \RuntimeException("Parameter array must have only one key, 'type'.");
        }
        $type = $v['type'];
        if (!is_string($type)) {
          throw new \RuntimeException("Parameter type must be a string.");
        }
        $parameters[$k] = ['type' => $type];
      }
      else {
        throw new \RuntimeException("Parameter must be an array or a string.");
      }
    }

    parent::__construct(['options' => $parameters]);
  }

}
