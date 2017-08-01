<?php

namespace Drupal\Tests\controller_annotations\Unit\EventSubscriber;

use Drupal\Core\Controller\ControllerBase;
use Drupal\controller_annotations\Configuration\Template;
use Drupal\controller_annotations\EventSubscriber\TemplateEventSubscriber;
use Drupal\controller_annotations\Templating\TemplateResolver;
use Drupal\Tests\UnitTestCase;
use Mockery as m;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @group controller_annotations
 */
class TemplateEventSubscriberTest extends UnitTestCase
{
    public function testOnKernelControllerWithoutTemplate()
    {
        $twig = m::mock(\Twig_Environment::class);
        $templateResolver = m::mock(TemplateResolver::class);

        $request = new Request();

        $event = m::mock(FilterControllerEvent::class);
        $event->shouldReceive('getRequest')->once()->andReturn($request);

        $eventSubscriber = new TemplateEventSubscriber($twig, $templateResolver);
        $this->assertNull($eventSubscriber->onKernelController($event));

        $event = m::mock(GetResponseForControllerResultEvent::class);
        $event->shouldReceive('getRequest')->once()->andReturn($request);

        $eventSubscriber = new TemplateEventSubscriber($twig, $templateResolver);
        $this->assertNull($eventSubscriber->onKernelView($event));
    }

    public function testOnKernelControllerWithInvalidTemplate()
    {
        $twig = m::mock(\Twig_Environment::class);
        $templateResolver = m::mock(TemplateResolver::class);

        $request = new Request();
        $request->attributes->set('_template', 'foo');

        $event = m::mock(FilterControllerEvent::class);
        $event->shouldReceive('getRequest')->once()->andReturn($request);

        $eventSubscriber = new TemplateEventSubscriber($twig, $templateResolver);
        $this->assertNull($eventSubscriber->onKernelController($event));

        $event = m::mock(GetResponseForControllerResultEvent::class);
        $event->shouldReceive('getRequest')->once()->andReturn($request);

        $eventSubscriber = new TemplateEventSubscriber($twig, $templateResolver);
        $this->assertNull($eventSubscriber->onKernelView($event));
    }

    public function testOnKernelControllerWithTemplate()
    {
        $templateName = 'resolved_template';

        $twig = m::mock(\Twig_Environment::class);
        $templateResolver = m::mock(TemplateResolver::class);
        $templateResolver->shouldReceive('resolveByControllerAndAction')->once()->andReturn($templateName);

        $template = new Template([]);

        $request = new Request();
        $request->attributes->set('_template', $template);

        $controller = m::mock(ControllerBase::class);
        $owner = [$controller, 'testAction'];

        $event = m::mock(FilterControllerEvent::class);
        $event->shouldReceive('getRequest')->once()->andReturn($request);
        $event->shouldReceive('getController')->once()->andReturn($owner);

        $eventSubscriber = new TemplateEventSubscriber($twig, $templateResolver);
        $eventSubscriber->onKernelController($event);

        $this->assertEquals($templateName, $template->getTemplate());
        $this->assertEquals($owner, $template->getOwner());
    }

    public function testOnKernelControllerWithTemplateName()
    {
        $templateName = 'resolved_template';

        $twig = m::mock(\Twig_Environment::class);
        $templateResolver = m::mock(TemplateResolver::class);
        $templateResolver->shouldReceive('normalize')->once()->andReturn($templateName);

        $template = new Template([
          'template' => $templateName
        ]);

        $request = new Request();
        $request->attributes->set('_template', $template);

        $controller = m::mock(ControllerBase::class);
        $owner = [$controller, 'testAction'];

        $event = m::mock(FilterControllerEvent::class);
        $event->shouldReceive('getRequest')->once()->andReturn($request);
        $event->shouldReceive('getController')->once()->andReturn($owner);

        $eventSubscriber = new TemplateEventSubscriber($twig, $templateResolver);
        $eventSubscriber->onKernelController($event);

        $this->assertEquals($templateName, $template->getTemplate());
        $this->assertEquals($owner, $template->getOwner());
    }

    public function testOnKernelView()
    {
        $renderedContent = 'rendered_page';
        $templateName = 'template.html.twig';

        $twig = m::mock(\Twig_Environment::class);
        $twig->shouldReceive('render')->once()->andReturn($renderedContent);

        $templateResolver = m::mock(TemplateResolver::class);

        $template = m::mock(Template::class);
        $template->shouldReceive('getOwner')->andReturn(['controller', 'action']);
        $template->shouldReceive('isStreamable')->andReturn(false);
        $template->shouldReceive('setOwner')->once()->withArgs([[]]);
        $template->shouldReceive('getTemplate')->once()->andReturn($templateName);

        $request = new Request();
        $request->attributes->set('_template', $template);

        $property = null;
        $value = null;

        $kernel = m::mock(HttpKernelInterface::class);
        $event = new GetResponseForControllerResultEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, []);

        $eventSubscriber = new TemplateEventSubscriber($twig, $templateResolver);
        $eventSubscriber->onKernelView($event);

        $response = $event->getResponse();
        $this->assertEquals($renderedContent, $response->getContent());
    }

    public function testOnKernelViewStreamed()
    {
        $templateName = 'template.html.twig';

        $twig = m::mock(\Twig_Environment::class);
        $twig->shouldReceive('display')->once()->withArgs([$templateName, []]);

        $templateResolver = m::mock(TemplateResolver::class);

        $template = m::mock(Template::class);
        $template->shouldReceive('getOwner')->andReturn(['controller', 'action']);
        $template->shouldReceive('isStreamable')->andReturn(true);
        $template->shouldReceive('setOwner')->once()->withArgs([[]]);
        $template->shouldReceive('getTemplate')->once()->andReturn($templateName);

        $request = new Request();
        $request->attributes->set('_template', $template);

        $property = null;
        $value = null;

        $kernel = m::mock(HttpKernelInterface::class);
        $event = new GetResponseForControllerResultEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, []);

        $eventSubscriber = new TemplateEventSubscriber($twig, $templateResolver);
        $eventSubscriber->onKernelView($event);

        $response = $event->getResponse();
        $this->assertEquals(false, $response->getContent());
        $this->assertInstanceOf(StreamedResponse::class, $response);

        $response->sendContent();
    }

    public function tearDown()
    {
        m::close();
    }
}
