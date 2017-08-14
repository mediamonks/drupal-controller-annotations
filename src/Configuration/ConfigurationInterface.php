<?php

namespace Drupal\controller_annotations\Configuration;

/**
 * ConfigurationInterface.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface ConfigurationInterface
{
    /**
     * Returns the alias name for an annotated configuration.
     *
     * @return string
     */
    public function getAliasName();

    /**
     * Returns whether multiple annotations of this type are allowed.
     *
     * @return bool
     */
    public function allowArray();
}
