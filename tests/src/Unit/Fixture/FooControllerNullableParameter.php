<?php

namespace Drupal\Tests\controller_annotations\Unit\Fixture;

class FooControllerNullableParameter
{
    public function requiredParamAction(\DateTime $param)
    {
    }

    public function defaultParamAction(\DateTime $param = null)
    {
    }

    public function nullableParamAction(?\DateTime $param)
    {
    }
}
