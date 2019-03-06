<?php

namespace Drupal\controller_annotations\Configuration;

use Doctrine\Common\Annotations\AnnotationRegistry;

class ConfigurationLoader
{
    /**
     * The annotation registry does not seem configured yet at this point so we need to do it ourselves
     */
    public static function load()
    {
        AnnotationRegistry::registerLoader('class_exists');
    }
}
