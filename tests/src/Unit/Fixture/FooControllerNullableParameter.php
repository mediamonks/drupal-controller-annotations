<?php

namespace Drupal\Tests\controller_annotations\Unit\Fixture;

class FooControllerNullableParameter {

  public function requiredParamAction(\DateTime $param) {
  }

  public function defaultParamAction(\DateTime $param = NULL) {
  }

  public function nullableParamAction(?\DateTime $param) {
  }

}
