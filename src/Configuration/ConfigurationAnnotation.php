<?php

namespace Drupal\controller_annotations\Configuration;

/**
 * Base configuration annotation.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class ConfigurationAnnotation implements ConfigurationInterface
{
    public function __construct(array $values)
    {
        foreach ($values as $k => $v) {
            if (!method_exists($this, $name = 'set'.$k)) {
                throw new \RuntimeException(sprintf('Unknown key "%s" for annotation "@%s".', $k, get_class($this)));
            }

            $this->$name($v);
        }
    }
}
