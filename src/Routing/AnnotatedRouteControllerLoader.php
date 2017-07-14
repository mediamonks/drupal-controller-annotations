<?php

namespace Drupal\controller_annotations\Routing;

use Drupal\controller_annotations\Configuration\Method;
use Drupal\controller_annotations\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route as FrameworkExtraBundleRoute;
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
        // controller
        $classAnnot = $this->reader->getClassAnnotation($class, $this->routeAnnotationClass);
        if ($classAnnot instanceof FrameworkExtraBundleRoute && $service = $classAnnot->getService()) {
            $route->setDefault('_controller', $service.':'.$method->getName());
        } else {
            $route->setDefault('_controller', $class->getName().'::'.$method->getName());
        }

        // requirements (@Method)
        foreach ($this->reader->getMethodAnnotations($method) as $configuration) {
            if ($configuration instanceof Method) {
                // we need to make sure this is an array instead of a string which is different in Symfony Framework
                // otherwise the support for defining an array of methods will not work as expected
                $route->setMethods($configuration->getMethods());
            } elseif ($configuration instanceof FrameworkExtraBundleRoute && $configuration->getService()) {
                throw new \LogicException('The service option can only be specified at class level.');
            }

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
