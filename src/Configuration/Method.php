<?php

namespace Drupal\controller_annotations\Configuration;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method as BaseMethod;
use Symfony\Component\Routing\Route;

/**
 * @Annotation
 */
class Method extends BaseMethod implements RouteModifierInterface
{
    /**
     * we need to make sure this is an array instead of a string which is different in Symfony Framework
     * otherwise the support for defining an array of methods will not work as expected
     *
     * @param Route $route
     */
    public function modifyRoute(Route $route)
    {
        $route->setMethods($this->getMethods());
    }
}
