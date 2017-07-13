<?php

namespace Drupal\controller_annotations\Routing;

use Drupal\controller_annotations\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Routing\AnnotatedRouteControllerLoader as BaseAnnotatedRouteControllerLoader;
use Symfony\Component\Routing\Route;

class AnnotatedRouteControllerLoader extends BaseAnnotatedRouteControllerLoader
{
    /**
     * @param Route $route
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     * @param mixed $annot
     */
    protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, $annot)
    {
        parent::configureRoute($route, $class, $method, $annot);

        foreach ($this->reader->getMethodAnnotations($method) as $configuration) {
            if ($configuration instanceof Security) {
                if ($configuration->isAccess()) {
                    $route->setRequirement('_access', 'TRUE');
                }
                if ($configuration->hasPermission()) {
                    $route->setRequirement('_permission', $configuration->getPermission());
                }
                if ($configuration->hasRole()) {
                    $route->setRequirement('_role', $configuration->getRole());
                }
                if ($configuration->hasAuth()) {
                    $route->setRequirement('_auth', $configuration->getAuth());
                }
                if ($configuration->hasCsrf()) {
                    $route->setRequirement('_csrf_token', 'TRUE');
                }
                if ($configuration->hasCustom()) {
                    $route->setRequirement('_custom_access', $configuration->getCustom());
                }
            }
        }
    }
}
