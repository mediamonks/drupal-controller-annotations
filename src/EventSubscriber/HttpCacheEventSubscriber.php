<?php

namespace Drupal\controller_annotations\EventSubscriber;

use Drupal\controller_annotations\Configuration\Cache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class HttpCacheEventSubscriber implements EventSubscriberInterface
{

    /**
     * @var \SplObjectStorage
     */
    private $lastModifiedDates;

    /**
     * @var \SplObjectStorage
     */
    private $eTags;

    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     */
    public function __construct()
    {
        $this->lastModifiedDates = new \SplObjectStorage();
        $this->eTags = new \SplObjectStorage();
    }

    /**
     * Handles HTTP validation headers.
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        if (!$configuration = $this->getConfiguration($request)) {
            return;
        }

        $response = new Response();

        if ($configuration->getLastModified()) {
            $this->setLastModified($request, $response, $configuration);
        }
        if ($configuration->getETag()) {
            $this->setETag($request, $response, $configuration);
        }
        if ($response->isNotModified($request)) {
            $event->setController(
                function () use ($response) {
                    return $response;
                }
            );
            $event->stopPropagation();
        }
    }

    /**
     * Modifies the response to apply HTTP cache headers when needed.
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$configuration = $this->getConfiguration($request)) {
            return;
        }

        $response = $event->getResponse();
        if ($this->hasUncachableStatusCode($response)) {
            return;
        }

        $this->setCacheProperties($request, $response, $configuration);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param Cache $configuration
     */
    protected function setLastModified(
        Request $request,
        Response $response,
        Cache $configuration
    ) {
        $lastModifiedDate = $this->getExpressionLanguage()->evaluate(
            $configuration->getLastModified(),
            $request->attributes->all()
        );
        $response->setLastModified($lastModifiedDate);
        $this->lastModifiedDates[$request] = $lastModifiedDate;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param Cache $configuration
     */
    protected function setETag(
        Request $request,
        Response $response,
        Cache $configuration
    ) {
        $eTag = $this->createETag($request, $configuration);
        $response->setETag($eTag);
        $this->eTags[$request] = $eTag;
    }

    /**
     * @param Request $request
     * @param Cache $configuration
     *
     * @return string
     */
    protected function createETag(Request $request, Cache $configuration)
    {
        return hash(
            'sha256',
            $this->getExpressionLanguage()->evaluate(
                $configuration->getETag(),
                $request->attributes->all()
            )
        );
    }

    /**
     * @param $age
     *
     * @return float
     */
    protected function calculateAge($age)
    {
        $now = microtime(true);

        return ceil(strtotime($age, $now) - $now);
    }

    /**
     * @param Request $request
     *
     * @return Cache|false
     */
    protected function getConfiguration(Request $request)
    {
        $configuration = $request->attributes->get('_cache');
        if (empty($configuration) || !$configuration instanceof Cache) {
            return false;
        }

        return $configuration;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param Cache $configuration
     */
    protected function setCacheProperties(
        Request $request,
        Response $response,
        Cache $configuration
    ) {
        if (null !== $age = $configuration->getSMaxAge()) {
            if (!is_numeric($age)) {
                $age = $this->calculateAge($configuration->getSMaxAge());
            }

            $response->setSharedMaxAge($age);
        }

        if (null !== $age = $configuration->getMaxAge()) {
            if (!is_numeric($age)) {
                $age = $this->calculateAge($configuration->getMaxAge());
            }

            $response->setMaxAge($age);
        }

        if (null !== $configuration->getExpires()) {
            $response->setExpires($this->calculateExpires($configuration));
        }

        if (null !== $configuration->getVary()) {
            $response->setVary($configuration->getVary());
        }

        if ($configuration->isPublic()) {
            $response->setPublic();
        }

        if ($configuration->isPrivate()) {
            $response->setPrivate();
        }

        if (isset($this->lastModifiedDates[$request])) {
            $response->setLastModified($this->lastModifiedDates[$request]);

            unset($this->lastModifiedDates[$request]);
        }

        if (isset($this->eTags[$request])) {
            $response->setETag($this->eTags[$request]);

            unset($this->eTags[$request]);
        }
    }

    /**
     * @param Cache $configuration
     *
     * @return bool|\DateTime
     */
    protected function calculateExpires(Cache $configuration)
    {
        return \DateTime::createFromFormat(
            'U',
            strtotime($configuration->getExpires()),
            new \DateTimeZone('UTC')
        );
    }

    /**
     * http://tools.ietf.org/html/draft-ietf-httpbis-p4-conditional-12#section-3.1
     *
     * @param Response $response
     *
     * @return bool
     */
    protected function hasUncachableStatusCode(Response $response)
    {
        if (!in_array(
            $response->getStatusCode(),
            [200, 203, 300, 301, 302, 304, 404, 410]
        )) {
            return true;
        }

        return false;
    }

    /**
     * @codeCoverageIgnore
     * @return ExpressionLanguage
     */
    private function getExpressionLanguage()
    {
        if (null === $this->expressionLanguage) {
            if (!class_exists(ExpressionLanguage::class)) {
                throw new \RuntimeException(
                    'Unable to use expressions as the Symfony ExpressionLanguage component is not installed.'
                );
            }
            $this->expressionLanguage = new ExpressionLanguage();
        }

        return $this->expressionLanguage;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => [
                ['onKernelController', 0],
            ],
            KernelEvents::RESPONSE => [
                ['onKernelResponse', 100],
            ],
        ];
    }
}
