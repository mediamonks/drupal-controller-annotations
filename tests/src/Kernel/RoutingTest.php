<?php

namespace Drupal\Tests\controller_annotations\Kernel;

use Symfony\Component\HttpFoundation\Request;

/**
 * @group controller_annotations
 */
class RoutingTest extends KernelTestBase
{
    public function testRoutingResponses()
    {
        $response = $this->request(Request::create('/test/basic'));
        $this->assertEquals('OK', $response->getContent());

        $this->assertResponseContents(Request::create('/test/method', 'GET'), 'ClassRouteController::getAction');
        $this->assertResponseContents(Request::create('/test/method', 'POST'), 'ClassRouteController::postAction');
        $this->assertMethodNotAllowed(Request::create('/test/method', 'DELETE'));

        $path = '/test/method/multiple';
        $contents = 'ClassRouteController::getAndPostAction';
        $this->assertResponseContents(Request::create($path, 'GET'), $contents);
        $this->assertResponseContents(Request::create($path, 'POST'), $contents);
        $this->assertMethodNotAllowed(Request::create($path, 'DELETE'));

        $this->assertResponseContents(Request::create('/test/prefix'),'PrefixedBasicController::emptyRouteAction');
        $this->assertResponseContents(Request::create('/test/prefix/named'), 'PrefixedBasicController::namedRouteAction');
    }
}
