<?php

namespace Drupal\Tests\controller_annotations\Functional;

use Drupal\Tests\BrowserTestBase;

class BasicControllerTest extends BrowserTestBase
{
    /**
     * @var array
     */
    public static $modules = ['controller_annotations', 'controller_annotations_test'];

    public function testBasicResponse()
    {
        $this->assertEquals('OK', $this->drupalGet('/test/basic'));
    }
}
