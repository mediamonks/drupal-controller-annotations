<?php

namespace Drupal\controller_annotations\EventSubscriber;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\ClassUtils;
use Drupal\controller_annotations\Configuration\ConfigurationInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ControllerEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Modifies the Request object to apply configuration information found in
     * controllers annotations like the template to render or HTTP caching
     * configuration.
     *
     * @param FilterControllerEvent $event
     * @throws \ReflectionException
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller) && method_exists($controller, '__invoke')) {
            $controller = array($controller, '__invoke');
        }

        if (!is_array($controller)) {
            return;
        }

        $className = ClassUtils::getClass($controller[0]);
        $object = new \ReflectionClass($className);
        $method = $object->getMethod($controller[1]);

        $classConfigurations = $this->getConfigurations($this->reader->getClassAnnotations($object));
        $methodConfigurations = $this->getConfigurations($this->reader->getMethodAnnotations($method));

        $this->setRequestAttributes(
            $event->getRequest(),
            $this->mergeConfigurations($classConfigurations, $methodConfigurations)
        );
    }

    /**
     * @param Request $request
     * @param array $configurations
     */
    protected function setRequestAttributes(Request $request, array $configurations)
    {
        foreach ($configurations as $key => $attributes) {
            $request->attributes->set($key, $attributes);
        }
    }

    /**
     * @param array $classConfigurations
     * @param array $methodConfigurations
     *
     * @return array
     */
    protected function mergeConfigurations(array $classConfigurations, array $methodConfigurations)
    {
        $configurations = [];
        foreach (array_merge(array_keys($classConfigurations), array_keys($methodConfigurations)) as $key) {
            if (!array_key_exists($key, $classConfigurations)) {
                $configurations[$key] = $methodConfigurations[$key];
            } elseif (!array_key_exists($key, $methodConfigurations)) {
                $configurations[$key] = $classConfigurations[$key];
            } else {
                if (is_array($classConfigurations[$key])) {
                    if (!is_array($methodConfigurations[$key])) {
                        throw new \UnexpectedValueException(
                            'Configurations should both be an array or both not be an array'
                        );
                    }
                    $configurations[$key] = array_merge($classConfigurations[$key], $methodConfigurations[$key]);
                } else {
                    // method configuration overrides class configuration
                    $configurations[$key] = $methodConfigurations[$key];
                }
            }
        }

        return $configurations;
    }

    /**
     * @param array $annotations
     *
     * @return array
     */
    protected function getConfigurations(array $annotations)
    {
        $configurations = [];
        foreach ($annotations as $configuration) {
            if ($configuration instanceof ConfigurationInterface) {
                if ($configuration->allowArray()) {
                    $configurations['_'.$configuration->getAliasName()][] = $configuration;
                } elseif (!isset($configurations['_'.$configuration->getAliasName()])) {
                    $configurations['_'.$configuration->getAliasName()] = $configuration;
                } else {
                    throw new \LogicException(
                        sprintf('Multiple "%s" annotations are not allowed.', $configuration->getAliasName())
                    );
                }
            }
        }

        return $configurations;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => [
                ['onKernelController', 200],
            ],
        ];
    }
}
