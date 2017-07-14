<?php

namespace Drupal\Tests\controller_annotations\Unit\EventSubscriber;

use Drupal\Core\Routing\RouteBuildEvent;
use Drupal\controller_annotations\EventSubscriber\RouteEventSubscriber;
use Drupal\Tests\UnitTestCase;
use Mockery as m;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @group controller_annotations
 */
class RouteEventSubscriberTest extends UnitTestCase
{
    /**
     * @var RouteCollection
     */
    protected $routeCollection;

    /**
     * @var RouteCollection
     */
    protected $annotatedRouteCollection;

    /**
     * @var RouteEventSubscriber
     */
    protected $eventSubscriber;

    protected function setUpEventSubscriber()
    {
        $annotationDirectoryLoader = m::mock(AnnotationDirectoryLoader::class);
        $annotationDirectoryLoader->shouldReceive('load')->andReturn($this->getAnnotatedRouteCollection());

        $this->eventSubscriber = new RouteEventSubscriber($annotationDirectoryLoader, '');
    }

    /**
     * @return RouteCollection
     */
    protected function getRouteCollection()
    {
        if (empty($this->routeCollection)) {
            $this->routeCollection = new RouteCollection;
        }

        return $this->routeCollection;
    }

    /**
     * @return RouteCollection
     */
    protected function getAnnotatedRouteCollection()
    {
        if (empty($this->annotatedRouteCollection)) {
            $this->annotatedRouteCollection = new RouteCollection;
        }

        return $this->annotatedRouteCollection;
    }

    protected function triggerOnRoutes()
    {
        if (empty($this->eventSubscriber)) {
            $this->setUpEventSubscriber();
        }
        $this->eventSubscriber->onRoutes(new RouteBuildEvent($this->getRouteCollection()));
    }

    public function testOnRoutesWithEmptyRouteCollection()
    {
        $this->triggerOnRoutes();
        $this->assertEquals(0, $this->getRouteCollection()->count());
    }

    public function testOnRoutesWithoutAnnotatedRoutes()
    {
        $route = new Route('/foo');
        $route->setOption('type', 'annotation');
        $route->setOption('path', 'foo');

        $this->getRouteCollection()->add('foo', new Route('/foo'));

        $this->triggerOnRoutes();
        $this->assertEquals(1, $this->getRouteCollection()->count());
    }

    public function testOnRoutesWithAnnotatedRoute()
    {
        $annotatedRoute = new Route('/bar');

        $annotatedRouteCollection = $this->getAnnotatedRouteCollection();
        $annotatedRouteCollection->add('bar', $annotatedRoute);

        $route = new Route('/foo');
        $route->setOption('type', 'annotation');
        $route->setOption('path', 'foo');

        $this->getRouteCollection()->add('foo', $route);
        $this->triggerOnRoutes();
        $this->assertEquals(1, $this->getRouteCollection()->count());
        $this->assertEquals($annotatedRoute, $this->getRouteCollection()->all()['bar']);
    }

    public function testOnRoutesWithoutRequiredOptions()
    {
        $this->setExpectedException(\Exception::class);

        $annotatedRoute = new Route('/bar');

        $annotatedRouteCollection = $this->getAnnotatedRouteCollection();
        $annotatedRouteCollection->add('bar', $annotatedRoute);

        $route = new Route('/foo');
        $route->setOption('type', 'annotation');

        $this->getRouteCollection()->add('foo', $route);

        $this->triggerOnRoutes();
    }
}
