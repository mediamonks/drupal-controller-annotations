<?php

namespace Drupal\controller_annotations\EventSubscriber;

use Drupal\controller_annotations\Configuration\Template;
use Drupal\controller_annotations\Templating\TemplateResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TemplateEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var TemplateResolver
     */
    private $resolver;

    /**
     * @param \Twig_Environment $twig
     * @param TemplateResolver $resolver
     */
    public function __construct(\Twig_Environment $twig, TemplateResolver $resolver)
    {
        $this->twig = $twig;
        $this->resolver = $resolver;
    }

    /**
     * Guesses the template name to render and its variables and adds them to
     * the request object.
     *
     * @param FilterControllerEvent $event A FilterControllerEvent instance
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $template = $this->getTemplateFromRequest($event);
        if (!$template instanceof Template) {
            return;
        }

        $template->setOwner($event->getController());
        $this->normalizeTemplate($template);
    }

    /**
     * Renders the template and initializes a new response object with the
     * rendered template content.
     *
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $template = $this->getTemplateFromRequest($event);
        if (!$template instanceof Template) {
            return;
        }

        $this->setResponse($event, $template, $this->getParameters($event, $template));
    }

    /**
     * @param KernelEvent $event
     * @return mixed
     */
    private function getTemplateFromRequest(KernelEvent $event)
    {
        return $event->getRequest()->attributes->get('_template');
    }

    /**
     * @param Template $template
     */
    private function normalizeTemplate(Template $template)
    {
        if (is_null($template->getTemplate())) {
            $templateFile = $this->resolver->resolveByControllerAndAction(
                get_class($template->getOwner()[0]),
                $template->getOwner()[1]
            );
        } else {
            $templateFile = $this->resolver->normalize($template->getTemplate());
        }

        $template->setTemplate($templateFile);
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     * @param Template $template
     * @param $parameters
     */
    private function setResponse(GetResponseForControllerResultEvent $event, Template $template, $parameters)
    {
        // make sure the owner (controller+dependencies) is not cached or stored elsewhere
        $template->setOwner([]);

        if ($template->isStreamable()) {
            $callback = function () use ($template, $parameters) {
                $this->twig->display($template->getTemplate(), $parameters);
            };

            $event->setResponse(new StreamedResponse($callback));
        } else {
            $event->setResponse(new Response($this->twig->render($template->getTemplate(), $parameters)));
        }
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     * @param Template $template
     * @return array|mixed
     */
    private function getParameters(GetResponseForControllerResultEvent $event, Template $template)
    {
        $parameters = $event->getControllerResult();

        $owner = $template->getOwner();
        list($controller, $action) = $owner;

        // when the annotation declares no default vars and the action returns
        // null, all action method arguments are used as default vars
        if (null === $parameters) {
            $parameters = $this->resolveDefaultParameters($event->getRequest(), $template, $controller, $action);
        }

        return $parameters;
    }

    /**
     * @param Request $request
     * @param Template $template
     * @param object $controller
     * @param string $action
     * @return array
     */
    private function resolveDefaultParameters(Request $request, Template $template, $controller, $action)
    {
        $arguments = $template->getVars();

        if (0 === count($arguments)) {
            $r = new \ReflectionObject($controller);

            $arguments = [];
            foreach ($r->getMethod($action)->getParameters() as $param) {
                $arguments[] = $param;
            }
        }

        return $this->resolveParametersWithReflection($request, $arguments);
    }

    /**
     * fetch the arguments of @Template.vars or everything if desired
     * and assign them to the designated template
     *
     * @param Request $request
     * @param array $arguments
     * @return array
     */
    private function resolveParametersWithReflection(Request $request, array $arguments)
    {
        $parameters = [];
        foreach ($arguments as $argument) {
            if ($argument instanceof \ReflectionParameter) {
                $name = $argument->getName();
                $parameters[$name] = !$request->attributes->has($name)
                && $argument->isDefaultValueAvailable()
                    ? $argument->getDefaultValue()
                    : $request->attributes->get($name);
            } else {
                $parameters[$argument] = $request->attributes->get($argument);
            }
        }

        return $parameters;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController', 100],
            KernelEvents::VIEW => ['onKernelView', 10],
        ];
    }
}
