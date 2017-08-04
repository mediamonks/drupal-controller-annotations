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

        self::registerDirectoryRecursive(dirname(__DIR__) . '/RouteModifier');
    }

    /**
     * @param string $dir
     */
    private static function registerDirectoryRecursive($dir) {

        foreach (scandir($dir,0) as $entry) {
            if ('.' === $entry[0]) {
                continue;
            }
            $path = $dir . '/' . $entry;
            if (is_file($path)) {
                require_once $path;
            }
            elseif (is_dir($path)) {
                self::registerDirectoryRecursive($path);
            }
        }
    }
}
