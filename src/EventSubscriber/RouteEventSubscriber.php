<?php

namespace Drupal\controller_annotations\EventSubscriber;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Drupal\Core\Routing\RouteBuildEvent;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Routing\Route;

class RouteEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var AnnotationDirectoryLoader
     */
    private $annotationDirectoryLoader;

    /**
     * @var string
     */
    private $rootPath;

    /**
     * @param AnnotationDirectoryLoader $annotationDirectoryLoader
     * @param string $rootPath
     */
    public function __construct(AnnotationDirectoryLoader $annotationDirectoryLoader, string $rootPath)
    {
        $this->registerAnnotations();
        $this->annotationDirectoryLoader = $annotationDirectoryLoader;
        $this->rootPath = $rootPath;
    }

    /**
     * Configure the annotation registry to make routing annotations available
     */
    private function registerAnnotations()
    {
        AnnotationRegistry::registerLoader('class_exists');
    }

    /**
     * @param RouteBuildEvent $event
     * @throws \Exception
     */
    public function onRoutes(RouteBuildEvent $event)
    {
        /**
         * @var $route Route
         */
        foreach ($event->getRouteCollection() as $name => $route) {
            if ($route->hasOption('type')
                && $route->getOption('type') === 'annotation'
            ) {
                $routeCollection = $this->annotationDirectoryLoader->load($this->rootPath.$this->getRoutePath($route));
                $routeCollection->addPrefix($route->getPath());

                $event->getRouteCollection()->addCollection($routeCollection);
                $event->getRouteCollection()->remove($name);
            }
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            RoutingEvents::DYNAMIC => [
                ['onRoutes', 0],
            ],
        ];
    }

    /**
     * @param Route $route
     * @return string
     * @throws \Exception
     */
    protected function getRoutePath(Route $route)
    {
        if ($route->hasOption('path')) {
            $path = $route->getOption('path');
        } elseif ($route->hasOption('module')) {
            $path = sprintf('/%s/src/Controller', drupal_get_path('module', $route->getOption('module')));
        } else {
            throw new \Exception(
                'Either the "resource" or "module"  option is required to load from annotations'
            );
        }

        return $path;
    }
}
