<?php

namespace Drupal\Tests\controller_annotations\Functional;

/**
 * @group controller_annotations
 */
class BasicControllerTest extends AbstractApiTestBase
{
    public function testBasicResponse()
    {
        $this->assertResponseContents('GET', '/test/basic', 'OK');
    }
}
