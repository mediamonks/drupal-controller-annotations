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
        $request = $event->getRequest();
        $template = $request->attributes->get('_template');

        if (!$template instanceof Template) {
            return;
        }

        $template->setOwner($event->getController());
        $this->normalizeTemplate($template);
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
     * Renders the template and initializes a new response object with the
     * rendered template content.
     *
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        /* @var Template $template */
        $request = $event->getRequest();
        $template = $request->attributes->get('_template');

        if (!$template instanceof Template) {
            return;
        }

        $parameters = $event->getControllerResult();

        $owner = $template->getOwner();
        list($controller, $action) = $owner;

        // when the annotation declares no default vars and the action returns
        // null, all action method arguments are used as default vars
        if (null === $parameters) {
            $parameters = $this->resolveDefaultParameters($request, $template, $controller, $action);
        }

        // make sure the owner (controller+dependencies) is not cached or stored elsewhere
        $template->setOwner([]);

        if ($template->isStreamable()) {
            $callback = function () use ($template, $parameters) {
                $this->twig->display($template->getTemplate(), $parameters);
            };

            $event->setResponse(new StreamedResponse($callback));
        }
        else {
            $event->setResponse(new Response($this->twig->render($template->getTemplate(), $parameters)));
        }
    }

    /**
     * @param Request $request
     * @param Template $template
     * @param string $controller
     * @param string $action
     * @return array
     */
    private function resolveDefaultParameters(Request $request, Template $template, $controller, $action)
    {
        $parameters = [];
        $arguments = $template->getVars();

        if (0 === count($arguments)) {
            $r = new \ReflectionObject($controller);

            $arguments = [];
            foreach ($r->getMethod($action)->getParameters() as $param) {
                $arguments[] = $param;
            }
        }

        // fetch the arguments of @Template.vars or everything if desired
        // and assign them to the designated template
        foreach ($arguments as $argument) {
            if ($argument instanceof \ReflectionParameter) {
                $parameters[$name = $argument->getName()] = !$request->attributes->has(
                    $name
                ) && $argument->isDefaultValueAvailable() ? $argument->getDefaultValue() : $request->attributes->get(
                    $name
                );
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
