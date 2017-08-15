<?php

namespace Drupal\Tests\controller_annotations\Unit;

class Helper
{
    public static function getProtectedMethod($class, $name)
    {
        $class = new \ReflectionClass($class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }
}
