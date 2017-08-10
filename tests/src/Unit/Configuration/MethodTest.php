<?php

namespace Drupal\Tests\controller_annotations\Unit\EventSubscriber;

use Drupal\controller_annotations\Configuration\Method;
use Drupal\Tests\UnitTestCase;
use Mockery as m;
use Symfony\Component\Routing\Route;

class MethodTest extends UnitTestCase
{
    public function testModifyRouteClass()
    {
        $route = m::mock(Route::class);
        $route->shouldReceive('setMethods')->once()->withArgs([['GET', 'POST']]);

        $class = m::mock(\ReflectionClass::class);
        $method = m::mock(\ReflectionMethod::class);

        $methodConfig = new Method(['methods' => ['GET', 'POST']]);
        $this->assertNull($methodConfig->modifyRouteClass($route, $class, $method));

        m::close();
    }

    public function testModifyRouteMethod()
    {
        $route = m::mock(Route::class);
        $route->shouldReceive('setMethods')->once()->withArgs([['GET', 'POST']]);

        $class = m::mock(\ReflectionClass::class);
        $method = m::mock(\ReflectionMethod::class);

        $methodConfig = new Method(['methods' => ['GET', 'POST']]);
        $this->assertNull($methodConfig->modifyRouteMethod($route, $class, $method));

        m::close();
    }

    public function testModify()
    {
        $route = m::mock(Route::class);
        $route->shouldReceive('setMethods')->once()->withArgs([['GET']]);
        $route->shouldReceive('setMethods')->once()->withArgs([['POST']]);

        $class = m::mock(\ReflectionClass::class);
        $method = m::mock(\ReflectionMethod::class);

        $methodConfig = new Method(['methods' => ['GET']]);
        $this->assertNull($methodConfig->modifyRouteClass($route, $class, $method));

        $methodConfig = new Method(['methods' => ['POST']]);
        $this->assertNull($methodConfig->modifyRouteMethod($route, $class, $method));

        m::close();
    }
}
