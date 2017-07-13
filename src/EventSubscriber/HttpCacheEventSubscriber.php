<?php

namespace Drupal\controller_annotations\EventSubscriber;

use Sensio\Bundle\FrameworkExtraBundle\EventListener\HttpCacheListener;
use Symfony\Component\HttpKernel\KernelEvents;

class HttpCacheEventSubscriber extends HttpCacheListener
{
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
