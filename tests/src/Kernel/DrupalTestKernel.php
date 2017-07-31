<?php

namespace Drupal\Tests\controller_annotations\Kernel;

use Drupal\Core\DrupalKernel;
use Drupal\Core\Site\Settings;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @param Request $request
     */
    protected function initializeSettings(Request $request)
    {
        $settings = Settings::getAll();
        parent::initializeSettings($request);
        new Settings($settings);
    }
}
