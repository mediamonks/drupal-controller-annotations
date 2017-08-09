<?php

namespace Drupal\controller_annotations\Configuration;

use Doctrine\Common\Annotations\AnnotationRegistry;

class ConfigurationLoader
{
    /**
     * Make all annotations available by loading the classes.
     * Registering the namespaces itself won't work since core resets the registry multiple times
     */
    public static function load()
    {
        AnnotationRegistry::registerFile(__DIR__.'/Cache.php');
        AnnotationRegistry::registerFile(__DIR__.'/Method.php');
        AnnotationRegistry::registerFile(__DIR__.'/ParamConverter.php');
        AnnotationRegistry::registerFile(__DIR__.'/Route.php');
        AnnotationRegistry::registerFile(__DIR__.'/Security.php');
        AnnotationRegistry::registerFile(__DIR__.'/Template.php');
    }
}
