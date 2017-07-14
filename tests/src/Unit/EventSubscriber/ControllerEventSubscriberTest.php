<?php

namespace Drupal\Tests\controller_annotations\Unit\EventSubscriber;

use Doctrine\Common\Annotations\Reader;
use Drupal\controller_annotations\EventSubscriber\ControllerEventSubscriber;
use Drupal\Tests\UnitTestCase;
use Mockery as m;
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
}
