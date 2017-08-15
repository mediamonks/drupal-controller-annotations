<?php

namespace Drupal\Tests\controller_annotations\Unit\Templating;

use Doctrine\Common\Annotations\Reader;
use Drupal\controller_annotations\Configuration\Method;
use Drupal\controller_annotations\Routing\AnnotatedRouteControllerLoader;
use Drupal\Tests\controller_annotations\Unit\Helper;
use Drupal\Tests\UnitTestCase;
use Mockery as m;
use Symfony\Component\Routing\Route;

/**
 * @group controller_annotations
 */
class AnnotatedRouteControllerLoaderTest extends UnitTestCase
{
    public function testConfigureRoute()
    {
        $routeConfiguration = m::mock(\Drupal\controller_annotations\Configuration\Route::class);
        $routeConfiguration->shouldReceive('getService')->andReturn(false);
        $routeConfiguration->shouldReceive('isAdmin')->andReturn(true);
        $routeConfiguration->shouldReceive('modifyRouteMethod')->andReturnNull();
        $routeConfiguration->shouldReceive('modifyRouteClass')->andReturnNull();

        $methodConfiguration = m::mock(Method::class);
        $methodConfiguration->shouldReceive('getMethods')->andReturn(['GET']);
        $methodConfiguration->shouldReceive('modifyRouteMethod')->andReturnNull();
        $methodConfiguration->shouldReceive('modifyRouteClass')->andReturnNull();

        $reader = m::mock(Reader::class);
        $reader->shouldReceive('getClassAnnotation')->andReturn($routeConfiguration);
        $reader->shouldReceive('getMethodAnnotations')->andReturn([
            $routeConfiguration,
            $methodConfiguration
        ]);
        $reader->shouldReceive('getClassAnnotations')->andReturn([]);

        $route = m::mock(Route::class);
        $route->shouldReceive('setDefault')->once();

        $reflectionClass = m::mock(\ReflectionClass::class);
        $reflectionClass->shouldReceive('getName')->once()->andReturn('Controller');

        $reflectionMethod = m::mock(\ReflectionMethod::class);
        $reflectionMethod->shouldReceive('getName')->once()->andReturn('action');

        $method = Helper::getProtectedMethod(AnnotatedRouteControllerLoader::class, 'configureRoute');
        $annotatedRouteControllerLoader = new AnnotatedRouteControllerLoader($reader);
        $method->invokeArgs($annotatedRouteControllerLoader, [$route, $reflectionClass, $reflectionMethod, null]);

        $this->assertTrue(true);

        m::close();
    }
}
