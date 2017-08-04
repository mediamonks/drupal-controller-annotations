<?php

namespace Drupal\controller_annotations\RouteModifier\Annotated;

use Symfony\Component\Routing\Route;

/**
 * @Annotation
 */
class RouteTitleMethod implements AnnotatedRouteModifierInterface
{
    /**
     * @var string
     */
    private $titleMethodName;

    /**
     * @param array $values
     *
     * @throws \RuntimeException
     */
    public function __construct(array $values)
    {
        if (!isset($values['value'])) {
            throw new \RuntimeException('Title callback must be specified.');
        }

        $titleMethodName = $values['value'];

        if (!is_string($titleMethodName)) {
            throw new \RuntimeException('Title method name must be a string.');
        }

        $this->titleMethodName = $titleMethodName;
    }

    /**
     * @param \Symfony\Component\Routing\Route $route
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     * @param mixed $annot
     *
     * @throws \RuntimeException
     */
    public function modifyAnnotatedRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, $annot)
    {
        if (!$class->hasMethod($this->titleMethodName)) {
            throw new \RuntimeException(
                sprintf(
                    'Title method %s does not exist on class %s.',
                    $this->titleMethodName,
                    $class->getName()));
        }

        $route->setDefault('_title_callback', $class->getName() . '::' . $this->titleMethodName);
    }
}
