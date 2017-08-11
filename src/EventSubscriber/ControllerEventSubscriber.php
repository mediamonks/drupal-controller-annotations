<?php

namespace Drupal\controller_annotations\EventSubscriber;

use Doctrine\Common\Annotations\Reader;
use Drupal\controller_annotations\Configuration\ConfigurationLoader;
use Sensio\Bundle\FrameworkExtraBundle\EventListener\ControllerListener;
use Symfony\Component\HttpKernel\KernelEvents;

class ControllerEventSubscriber extends ControllerListener
{
    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        parent::__construct($reader);

        ConfigurationLoader::load();
    }

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
