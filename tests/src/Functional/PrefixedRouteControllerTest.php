<?php

namespace Drupal\Tests\controller_annotations\Functional;

/**
 * @group controller_annotations
 */
class PrefixedRouteControllerTest extends AbstractApiTestBase
{
    public function testEmptyRouteAction()
    {
        $this->assertResponseContents('GET', 'test/prefix', 'PrefixedBasicController::emptyRouteAction');
    }

    public function testNamedRouteAction()
    {
        $this->assertResponseContents('GET', 'test/prefix/named', 'PrefixedBasicController::namedRouteAction');
    }
}
