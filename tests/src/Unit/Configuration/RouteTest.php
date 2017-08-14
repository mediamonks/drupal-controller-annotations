<?php

namespace Drupal\Tests\controller_annotations\Unit\EventSubscriber;

use Drupal\controller_annotations\Configuration\Route as RouteConfiguration;
use Drupal\Tests\UnitTestCase;
use Mockery as m;
use Symfony\Component\Routing\Route;

class RouteTest extends UnitTestCase
{
    public function testModifyRouteClass()
    {
        $route = m::mock(Route::class);
        $route->shouldReceive('setOption')->once()->withArgs(['_admin_route', true]);

        $class = m::mock(\ReflectionClass::class);
        $method = m::mock(\ReflectionMethod::class);

        $routeConfig = new RouteConfiguration(['admin' => true]);
        $this->assertNull($routeConfig->modifyRouteClass($route, $class, $method));

        m::close();
    }

    public function testModifyMethodClass()
    {
        $route = m::mock(Route::class);

        $class = m::mock(\ReflectionClass::class);
        $method = m::mock(\ReflectionMethod::class);

        $routeConfig = new RouteConfiguration([]);
        $this->assertNull($routeConfig->modifyRouteMethod($route, $class, $method));

        m::close();
    }

    public function testServiceNotAllowedOnMethodLevel()
    {
        $this->setExpectedException(\LogicException::class);

        $route = m::mock(Route::class);

        $class = m::mock(\ReflectionClass::class);
        $method = m::mock(\ReflectionMethod::class);

        $routeConfig = new RouteConfiguration(['service' => 'foo']);
        $this->assertNull($routeConfig->modifyRouteMethod($route, $class, $method));

        m::close();
    }

    public function testAllowArray()
    {
        $routeConfig = new RouteConfiguration([]);
        $this->assertTrue($routeConfig->allowArray());
    }

    public function testUnknownProperty()
    {
        $this->setExpectedException(\BadMethodCallException::class);
        new RouteConfiguration(['foo' => 'bar']);
    }
}
