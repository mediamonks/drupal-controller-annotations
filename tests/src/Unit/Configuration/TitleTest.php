<?php

namespace Drupal\Tests\controller_annotations\Unit\EventSubscriber;

use Drupal\controller_annotations\Configuration\Title;
use Drupal\Tests\UnitTestCase;
use Mockery as m;
use Symfony\Component\Routing\Route;

class TitleTest extends UnitTestCase
{
    public function testModifyRouteMethod()
    {
        $route = m::mock(Route::class);
        $route->shouldReceive('setDefault')->once()->withArgs(['_title', 'Hello World']);
        $route->shouldReceive('setDefault')->once()->withArgs(['_title_arguments', ['arguments' => true]]);
        $route->shouldReceive('setDefault')->once()->withArgs(['_title_context', ['context' => true]]);
        $route->shouldReceive('setDefault')->once()->withArgs(['_title_callback', 'foo::callback']);

        $class = m::mock(\ReflectionClass::class);
        $method = m::mock(\ReflectionMethod::class);

        $security = new Title([
            'value' => 'Hello World',
            'arguments' => ['arguments' => true],
            'context' => ['context' => true],
            'callback' => 'foo::callback'
        ]);
        $this->assertNull($security->modifyRouteMethod($route, $class, $method));

        m::close();
    }

    public function testModifyRouteMethodInlineAccess()
    {
        $route = m::mock(Route::class);
        $route->shouldReceive('setDefault')->once()->withArgs(['_title_callback', 'foo::callback']);

        $class = m::mock(\ReflectionClass::class);
        $class->shouldReceive('hasMethod')->andReturn('callback');
        $class->shouldReceive('getName')->andReturn('foo');
        $method = m::mock(\ReflectionMethod::class);

        $security = new Title([
            'callback' => 'callback'
        ]);
        $this->assertNull($security->modifyRouteClass($route, $class, $method));

        m::close();
    }

    public function testUnknownProperty()
    {
        $this->setExpectedException(\RuntimeException::class);
        new Title(['foo' => 'bar']);
    }
}
