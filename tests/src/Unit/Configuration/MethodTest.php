<?php

namespace Drupal\Tests\controller_annotations\Unit\EventSubscriber;

use Drupal\controller_annotations\Configuration\Method;
use Drupal\Tests\UnitTestCase;
use Mockery as m;
use Symfony\Component\Routing\Route;

class MethodTest extends UnitTestCase
{
    public function testModify()
    {
        $route = m::mock(Route::class);
        $route->shouldReceive('setMethods')->once()->withArgs([['GET', 'POST']]);

        $method = new Method(['methods' => ['GET', 'POST']]);
        $this->assertNull($method->modifyRoute($route));

        m::close();
    }
}
