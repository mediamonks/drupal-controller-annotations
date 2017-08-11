<?php

namespace Drupal\Tests\controller_annotations\Unit\EventSubscriber;

use Drupal\controller_annotations\Configuration\Security;
use Drupal\Tests\UnitTestCase;
use Mockery as m;
use Symfony\Component\Routing\Route;

class SecurityTest extends UnitTestCase
{
    public function testModifyRouteMethod()
    {
        $route = m::mock(Route::class);
        $route->shouldReceive('setRequirement')->once()->withArgs(['_access', true]);
        $route->shouldReceive('setRequirement')->once()->withArgs(['_permission', 'permission']);
        $route->shouldReceive('setRequirement')->once()->withArgs(['_role', 'role']);
        $route->shouldReceive('setRequirement')->once()->withArgs(['_entity_access', 'entity']);
        $route->shouldReceive('setRequirement')->once()->withArgs(['_csrf_token', true]);
        $route->shouldReceive('setRequirement')->once()->withArgs(['_custom_access', 'foo::custom']);

        $class = m::mock(\ReflectionClass::class);
        $method = m::mock(\ReflectionMethod::class);

        $security = new Security([
            'access' => true,
            'permission' => 'permission',
            'role' => 'role',
            'entity' => 'entity',
            'csrf' => true,
            'custom' => 'foo::custom'
        ]);
        $this->assertNull($security->modifyRouteMethod($route, $class, $method));

        m::close();
    }

    public function testModifyRouteMethodInlineAccess()
    {
        $route = m::mock(Route::class);
        $route->shouldReceive('setRequirement')->once()->withArgs(['_custom_access', 'foo::custom']);

        $class = m::mock(\ReflectionClass::class);
        $class->shouldReceive('hasMethod')->andReturn('custom');
        $class->shouldReceive('getName')->andReturn('foo');
        $method = m::mock(\ReflectionMethod::class);

        $security = new Security([
            'custom' => 'custom'
        ]);
        $this->assertNull($security->modifyRouteMethod($route, $class, $method));

        m::close();
    }
}
