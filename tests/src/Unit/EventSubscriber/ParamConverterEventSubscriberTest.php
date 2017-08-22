<?php

namespace Drupal\Tests\controller_annotations\Unit\EventSubscriber;

use Drupal\controller_annotations\Configuration\ParamConverter;
use Drupal\controller_annotations\EventSubscriber\ParamConverterEventSubscriber;
use Drupal\controller_annotations\Request\ParamConverter\ParamConverterManager;
use Drupal\Tests\controller_annotations\Unit\Fixture\FooControllerNullableParameter;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Mockery as m;

class ParamConverterEventSubscriberTest extends UnitTestCase
{

    /**
     * @dataProvider getControllerWithNoArgsFixtures
     */
    public function testRequestIsSkipped($controllerCallable)
    {
        $kernel = $this->getMockBuilder(HttpKernelInterface::class)->getMock();
        $request = new Request();

        $eventSubscriber = new ParamConverterEventSubscriber(
            $this->getParamConverterManager($request, [])
        );
        $event = new FilterControllerEvent(
            $kernel,
            $controllerCallable,
            $request,
            null
        );

        $eventSubscriber->onKernelController($event);
    }

    public function getControllerWithNoArgsFixtures()
    {
        return [
          [[new ParamConverterTestController(), 'noArgAction']],
          [new ParamConverterInvokableNoArgController()],
        ];
    }

    /**
     * @dataProvider getControllerWithArgsFixtures
     */
    public function testAutoConvert($controllerCallable)
    {
        $kernel = $this->getMockBuilder(
            HttpKernelInterface::class
        )->getMock();
        $request = new Request([], [], ['date' => '2014-03-14 09:00:00']);

        $converter = new ParamConverter(
            ['name' => 'date', 'class' => 'DateTime']
        );

        $eventSubscriber = new ParamConverterEventSubscriber(
            $this->getParamConverterManager($request, ['date' => $converter])
        );
        $event = new FilterControllerEvent(
            $kernel,
            $controllerCallable,
            $request,
            null
        );

        $eventSubscriber->onKernelController($event);
    }

    /**
     * @dataProvider settingOptionalParamProvider
     * @requires PHP 7.1
     */
    public function testSettingOptionalParam($function, $isOptional)
    {
        $kernel = $this->getMockBuilder(HttpKernelInterface::class)->getMock();
        $request = new Request();

        $converter = new ParamConverter(
            ['name' => 'param', 'class' => 'DateTime']
        );
        $converter->setIsOptional($isOptional);

        $eventSubscriber = new ParamConverterEventSubscriber(
            $this->getParamConverterManager($request, ['param' => $converter]),
            true
        );
        $event = new FilterControllerEvent(
            $kernel,
            [
            new FooControllerNullableParameter(),
            $function,
            ],
            $request,
            null
        );

        $eventSubscriber->onKernelController($event);
    }

    public function settingOptionalParamProvider()
    {
        return [
          ['requiredParamAction', false],
          ['defaultParamAction', true],
          ['nullableParamAction', true],
        ];
    }

    /**
     * @dataProvider getControllerWithArgsFixtures
     */
    public function testNoAutoConvert($controllerCallable)
    {
        $kernel = $this->getMockBuilder(HttpKernelInterface::class)->getMock();
        $request = new Request([], [], ['date' => '2014-03-14 09:00:00']);

        $eventSubscriber = new ParamConverterEventSubscriber(
            $this->getParamConverterManager($request, []),
            false
        );
        $event = new FilterControllerEvent(
            $kernel,
            $controllerCallable,
            $request,
            null
        );

        $eventSubscriber->onKernelController($event);
    }

    public function getControllerWithArgsFixtures()
    {
        return [
          [[new ParamConverterTestController(), 'dateAction']],
          [new ParamConverterInvokableController()],
        ];
    }

    protected function getParamConverterManager(Request $request, $configurations)
    {
        $manager = $this->getMockBuilder(ParamConverterManager::class)->getMock();
        $manager
          ->expects($this->once())
          ->method('apply')
          ->with($this->equalTo($request), $this->equalTo($configurations));

        return $manager;
    }

    public function testPredefinedConfigurations()
    {
        $configuration = m::mock(\stdClass::class);
        $configuration->shouldReceive('getName')->andReturn('foo');

        $configurations = [$configuration];

        $kernel = m::mock(HttpKernelInterface::class);
        $request = new Request();
        $request->attributes->set('_converters', $configurations);

        $event = new FilterControllerEvent(
            $kernel,
            'time',
            $request,
            null
        );

        $manager = m::mock(ParamConverterManager::class);
        $manager->shouldReceive('apply')->once()->withArgs([$request, ['foo' => $configuration]]);

        $eventSubscriber = new ParamConverterEventSubscriber($manager, false);
        $eventSubscriber->onKernelController($event);

        $this->assertNull(m::close());
    }
}

class ParamConverterTestController
{

    public function noArgAction(Request $request)
    {
    }

    public function dateAction(\DateTime $date)
    {
    }
}

class ParamConverterInvokableNoArgController
{

    public function __invoke(Request $request)
    {
    }
}

class ParamConverterInvokableController
{

    public function __invoke(\DateTime $date)
    {
    }
}
