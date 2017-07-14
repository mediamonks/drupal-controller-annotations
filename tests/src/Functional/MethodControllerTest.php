<?php

namespace Drupal\Tests\controller_annotations\Functional;

/**
 * @group controller_annotations
 */
class MethodControllerTest extends AbstractApiTestBase
{
    public function testGetAction()
    {
        $this->assertResponseContents('GET', '/test/method', 'ClassRouteController::getAction');
        $this->assertMethodNotAllowed('DELETE', '/test/method');
    }

    public function testPostAction()
    {
        $this->assertResponseContents('POST', '/test/method', 'ClassRouteController::postAction');
        $this->assertMethodNotAllowed('DELETE', '/test/method');
    }

    public function testGetPostAction()
    {
        $path = '/test/method/multiple';
        $contents = 'ClassRouteController::getAndPostAction';
        $this->assertResponseContents('GET', $path, $contents);
        $this->assertResponseContents('POST', $path, $contents);
        $this->assertMethodNotAllowed('DELETE', $path);
    }
}
