<?php

namespace Drupal\controller_annotations\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Sensio\Bundle\FrameworkExtraBundle\EventListener\ParamConverterListener;

class ParamConverterEventSubscriber extends ParamConverterListener
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => [
                ['onKernelController', 100],
            ],
        ];
    }
}
