<?php

namespace Drupal\controller_annotations\EventSubscriber;

use Drupal\Core\Routing\RouteBuildEvent;
use Drupal\Core\Routing\RoutingEvents;
use Drupal\controller_annotations\Configuration\ConfigurationLoader;
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
    public function __construct(AnnotationDirectoryLoader $annotationDirectoryLoader, $rootPath)
    {
        ConfigurationLoader::load();

        $this->annotationDirectoryLoader = $annotationDirectoryLoader;
        $this->rootPath = $rootPath;
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
                if ($route->hasOption('path')) {
                    $path = $route->getOption('path');
                } elseif ($route->hasOption('module')) {
                    $path = sprintf('/%s/src/Controller', drupal_get_path('module', $route->getOption('module')));
                } else {
                    throw new \Exception(
                        'Either the "resource" or "module"  option is required to load from annotations'
                    );
                }

                $routeCollection = $this->annotationDirectoryLoader->load($this->rootPath.$path);
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
            ]
        ];
    }
}
