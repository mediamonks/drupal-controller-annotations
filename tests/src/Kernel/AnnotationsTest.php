<?php

namespace Drupal\Tests\controller_annotations\Kernel;

use Drupal\Tests\controller_annotations\Kernel\TestUserSession as UserSession;
use Symfony\Component\HttpFoundation\Request;

/**
 * @group controller_annotations
 */
class AnnotationsTest extends KernelTestBase
{
    public function testRouting()
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

        $this->assertResponseContents(Request::create('/test/prefix'), 'PrefixedBasicController::emptyRouteAction');
        $this->assertResponseContents(Request::create('/test/prefix/named'), 'PrefixedBasicController::namedRouteAction');

        $this->assertResponseContents(Request::create('/test/service'), 'ServiceController::getAction');
    }

    public function testTemplate()
    {
        $sourceModule = $this->getDrupalRoot() . '/modules/controller_annotations/tests/modules/controller_annotations_test/templates/';
        $destinationModule = $this->getDrupalRoot() . '/modules/controller_annotations_test/templates/';

        if (!file_exists($destinationModule)) {
            mkdir($destinationModule, 0777, true);
        }
        foreach (new \DirectoryIterator($sourceModule) as $fileInfo) {
            if (!$fileInfo->isFile()) {
                continue;
            }
            copy($sourceModule . $fileInfo->getFilename(), $destinationModule . $fileInfo->getFilename());
        }

        $this->assertResponseContents(Request::create('/test/template/empty'), 'empty');
        $this->assertResponseContents(Request::create('/test/template/module-controller'), 'module-controller');
        $this->assertResponseContents(Request::create('/test/template/module-controller-action'), 'module-controller-action');
        $this->assertResponseContents(Request::create('/test/template/parameter'), 'value');
        $this->assertResponseContents(Request::create('/test/template/parameter-url/foo'), 'foo default');
    }

    public function testSecurity()
    {
        // all access
        $this->assertResponseContents(Request::create('/test/security/access'), 'OK');

        // only access with "access content" permission
        $this->assertForbidden(Request::create('/test/security/permission'));
        $this->setAccount(new UserSession([
            'uid' => 2,
            'permissions' => ['foo']
        ]));
        $this->assertForbidden(Request::create('/test/security/permission'));
        $this->setAccount(new UserSession([
            'uid' => 2,
            'permissions' => ['access content']
        ]));

        $this->assertResponseContents(Request::create('/test/security/permission'), 'OK');
        $this->setAdministratorAccount();
        $this->assertResponseContents(Request::create('/test/security/permission'), 'OK');

        // only access with "administrator" role
        $this->setAnonymousAccount();
        $this->assertForbidden(Request::create('/test/security/role'));
        $this->setAuthenticatedAccount();
        $this->assertForbidden(Request::create('/test/security/role'));
        $this->setAdministratorAccount();
        $this->assertResponseContents(Request::create('/test/security/role'), 'OK');
    }
}
