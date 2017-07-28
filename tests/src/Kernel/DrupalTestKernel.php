<?php

namespace Drupal\Tests\controller_annotations\Kernel;

use Drupal\Core\DrupalKernel;

class DrupalTestKernel extends DrupalKernel
{
    /**
     * {@inheritdoc}
     */
    public function setSitePath($path)
    {
        if (empty($this->sitePath)) {
            parent::setSitePath($path);
        }
    }
}
