<?php

namespace Drupal\Tests\controller_annotations\Unit\EventSubscriber;

use Drupal\controller_annotations\Configuration\Route as RouteConfiguration;
use Drupal\Tests\UnitTestCase;
use Mockery as m;
use Symfony\Component\Routing\Route;

class RouteTest extends UnitTestCase
{
    public function testModify()
    {
        $route = m::mock(Route::class);
        $route->shouldReceive('setOption')->once()->withArgs(['_admin_route', true]);

        $method = new RouteConfiguration(['admin' => true]);
        $this->assertNull($method->modifyRoute($route));

        m::close();
    }
}
