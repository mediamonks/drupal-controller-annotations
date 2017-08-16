<?php

namespace Drupal\Tests\controller_annotations\Unit\EventSubscriber;

use Doctrine\Common\Annotations\Reader;
use Drupal\controller_annotations\Configuration\ConfigurationInterface;
use Drupal\controller_annotations\EventSubscriber\ControllerEventSubscriber;
use Drupal\Tests\controller_annotations\Unit\Helper;
use Drupal\Tests\UnitTestCase;
use Mockery as m;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * @group controller_annotations
 */
class ControllerEventSubscriberTest extends UnitTestCase
{
    public function testOnKernelController()
    {
        $reader = m::mock(Reader::class);

        $eventSubscriber = new ControllerEventSubscriber($reader);

        $event = m::mock(FilterControllerEvent::class);
        $event->shouldReceive('getController')->once()->andReturn(null);

        $this->assertNull($eventSubscriber->onKernelController($event));
    }

    public function testControllerInvoke()
    {
        $reader = m::mock(Reader::class);
        $reader->shouldReceive('getClassAnnotations')->andReturn([]);
        $reader->shouldReceive('getMethodAnnotations')->andReturn([]);

        $eventSubscriber = new ControllerEventSubscriber($reader);

        $event = m::mock(FilterControllerEvent::class);
        $event->shouldReceive('getController')->once()->andReturn(new ControllerInvokableController);
        $event->shouldReceive('getRequest')->once()->andReturn(new Request);

        $this->assertNull($eventSubscriber->onKernelController($event));
    }

    public function testMultipleConfigurations()
    {
        $configuration = m::mock(ConfigurationInterface::class);
        $configuration->shouldReceive('allowArray')->andReturn(true);
        $configuration->shouldReceive('getAliasName')->andReturn('foo');

        $reader = m::mock(Reader::class);
        $reader->shouldReceive('getClassAnnotations')->andReturn([
          $configuration,
          $configuration
        ]);
        $reader->shouldReceive('getMethodAnnotations')->andReturn([]);

        $eventSubscriber = new ControllerEventSubscriber($reader);

        $event = m::mock(FilterControllerEvent::class);
        $event->shouldReceive('getController')->once()->andReturn(new ControllerInvokableController);
        $event->shouldReceive('getRequest')->once()->andReturn(new Request);

        $this->assertNull($eventSubscriber->onKernelController($event));
    }

    public function testMergeConfigurations()
    {
        $classConfigurations = [
          'foo' => 'bar'
        ];
        $methodConfigurations = [
          'foo' => 'bar'
        ];

        $reader = m::mock(Reader::class);
        $method = Helper::getProtectedMethod(ControllerEventSubscriber::class, 'mergeConfigurations');
        $eventSubscriber = new ControllerEventSubscriber($reader);
        $result = $method->invokeArgs($eventSubscriber, [$classConfigurations, $methodConfigurations]);
        $this->assertEquals(['foo' => 'bar'], $result);
    }

    public function testMergeConfigurationsArray()
    {
        $classConfigurations = [
          'foo' => ['bar']
        ];
        $methodConfigurations = [
          'foo' => ['baz']
        ];

        $reader = m::mock(Reader::class);
        $method = Helper::getProtectedMethod(ControllerEventSubscriber::class, 'mergeConfigurations');
        $eventSubscriber = new ControllerEventSubscriber($reader);
        $result = $method->invokeArgs($eventSubscriber, [$classConfigurations, $methodConfigurations]);

        $this->assertEquals(['foo' => ['bar', 'baz']], $result);
    }

    public function testMergeConfigurationsMismatch()
    {
        $this->setExpectedException(\UnexpectedValueException::class);

        $classConfigurations = [
          'foo' => ['bar']
        ];
        $methodConfigurations = [
          'foo' => 'bar'
        ];

        $reader = m::mock(Reader::class);
        $method = Helper::getProtectedMethod(ControllerEventSubscriber::class, 'mergeConfigurations');
        $eventSubscriber = new ControllerEventSubscriber($reader);
        $method->invokeArgs($eventSubscriber, [$classConfigurations, $methodConfigurations]);
    }
}

class ControllerInvokableController
{
    public function __invoke()
    {
    }
}
