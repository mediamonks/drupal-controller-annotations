<?php

namespace Drupal\Tests\controller_annotations\Unit\Templating;

use Doctrine\Common\Annotations\Reader;
use Drupal\controller_annotations\Configuration\Method;
use Drupal\controller_annotations\Routing\AnnotatedRouteControllerLoader;
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

        $methodConfiguration = m::mock(Method::class);
        $methodConfiguration->shouldReceive('getMethods')->andReturn(['GET']);

        $reader = m::mock(Reader::class);
        $reader->shouldReceive('getClassAnnotation')->andReturn($routeConfiguration);
        $reader->shouldReceive('getMethodAnnotations')->andReturn([
            $routeConfiguration,
            $methodConfiguration
        ]);

        $route = m::mock(Route::class);
        $route->shouldReceive('setDefault')->once();
        $route->shouldReceive('setOption')->once();
        $route->shouldReceive('setMethods')->once();

        $reflectionClass = m::mock(\ReflectionClass::class);
        $reflectionClass->shouldReceive('getName')->once()->andReturn('Controller');

        $reflectionMethod = m::mock(\ReflectionMethod::class);
        $reflectionMethod->shouldReceive('getName')->once()->andReturn('action');

        $method = self::getMethod(AnnotatedRouteControllerLoader::class, 'configureRoute');
        $annotatedRouteControllerLoader = new AnnotatedRouteControllerLoader($reader);
        $method->invokeArgs($annotatedRouteControllerLoader, [$route, $reflectionClass, $reflectionMethod, null]);

        $this->assertTrue(true);

        m::close();
    }

    protected static function getMethod($class, $name)
    {
        $class = new \ReflectionClass($class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }
}
