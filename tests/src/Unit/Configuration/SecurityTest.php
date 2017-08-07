<?php

namespace Drupal\Tests\controller_annotations\Unit\EventSubscriber;

use Drupal\controller_annotations\Configuration\Security;
use Drupal\Tests\UnitTestCase;
use Mockery as m;
use Symfony\Component\Routing\Route;

class SecurityTest extends UnitTestCase
{
    public function testModify()
    {
        $route = m::mock(Route::class);
        $route->shouldReceive('setRequirement')->once()->withArgs(['_access', true]);
        $route->shouldReceive('setRequirement')->once()->withArgs(['_permission', 'permission']);
        $route->shouldReceive('setRequirement')->once()->withArgs(['_role', 'role']);
        $route->shouldReceive('setRequirement')->once()->withArgs(['_entity_access', 'entity']);
        $route->shouldReceive('setRequirement')->once()->withArgs(['_csrf_token', true]);
        $route->shouldReceive('setRequirement')->once()->withArgs(['_custom_access', 'custom']);

        $method = new Security([
            'access' => true,
            'permission' => 'permission',
            'role' => 'role',
            'entity' => 'entity',
            'csrf' => true,
            'custom' => 'custom'
        ]);
        $this->assertNull($method->modifyRoute($route));

        m::close();
    }
}
