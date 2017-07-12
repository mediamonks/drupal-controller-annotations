<?php

namespace Drupal\controller_annotations\Configuration;

use Doctrine\Common\Annotations\AnnotationRegistry;

class ConfigurationLoader
{
    /**
     * Make all available annotations available by loading the classes.
     * Registering the namespaces itself won't work since core resets the registry multiple times
     */
    public static function load()
    {
        $configurationPath = __DIR__.'/../Configuration/';
        AnnotationRegistry::registerFile($configurationPath.'Cache.php');
        AnnotationRegistry::registerFile($configurationPath.'Method.php');
        AnnotationRegistry::registerFile($configurationPath.'ParamConverter.php');
        AnnotationRegistry::registerFile($configurationPath.'Route.php');
        AnnotationRegistry::registerFile($configurationPath.'Security.php');
        AnnotationRegistry::registerFile($configurationPath.'Template.php');
    }
}
