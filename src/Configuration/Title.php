<?php

namespace Drupal\controller_annotations\Configuration;

use Symfony\Component\Routing\Route as RoutingRoute;

/**
 * @Annotation
 */
class Title extends ConfigurationAnnotation implements RouteModifierMethodInterface, RouteModifierClassInterface
{
    /**
     * @var string
     */
    protected $value;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * @var array
     */
    protected $context;

    /**
     * @var string
     */
    protected $callback;

    /**
     * @param $title
     */
    public function setValue($title)
    {
        $this->setTitle($title);
    }

    /**
     * @return bool
     */
    public function hasTitle()
    {
        return !empty($this->value);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->value;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->value = $title;
    }

    /**
     * @return bool
     */
    public function hasArguments()
    {
        return !empty($this->arguments);
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @return bool
     */
    public function hasContext()
    {
        return !empty($this->context);
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param array $context
     */
    public function setContext(array $context)
    {
        $this->context = $context;
    }

    /**
     * @return bool
     */
    public function hasCallback()
    {
        return !empty($this->callback);
    }

    /**
     * @return string
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param string $callback
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return string
     */
    public function getAliasName()
    {
        return 'title';
    }

    /**
     * @return bool
     */
    public function allowArray()
    {
        return false;
    }

    /**
     * @param RoutingRoute $route
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     */
    public function modifyRouteClass(RoutingRoute $route, \ReflectionClass $class, \ReflectionMethod $method)
    {
        $this->modifyRoute($route, $class);
    }

    /**
     * @param RoutingRoute $route
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     */
    public function modifyRouteMethod(RoutingRoute $route, \ReflectionClass $class, \ReflectionMethod $method)
    {
        $this->modifyRoute($route, $class);
    }

    /**
     * @param RoutingRoute $route
     * @param \ReflectionClass $class
     */
    protected function modifyRoute(RoutingRoute $route, \ReflectionClass $class)
    {
        if ($this->hasTitle()) {
            $route->setDefault('_title', $this->getTitle());
        }
        if ($this->hasArguments()) {
            $route->setDefault('_title_arguments', $this->getArguments());
        }
        if ($this->hasContext()) {
            $route->setDefault('_title_context', $this->getContext());
        }

        $this->registerCallback($route, $class);
    }

    /**
     * @param RoutingRoute $route
     * @param \ReflectionClass $class
     */
    protected function registerCallback(RoutingRoute $route, \ReflectionClass $class)
    {
        if ($this->hasCallback()) {
            if (strpos($this->getCallback(), '::') === false && $class->hasMethod($this->getCallback())) {
                $this->setCallback(sprintf('%s::%s', $class->getName(), $this->getCallback()));
            }
            $route->setDefault('_title_callback', $this->getCallback());
        }
    }
}
