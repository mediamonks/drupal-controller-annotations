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
        $this->assertEquals('BasicController::basicAction', $response->getContent());

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

    public function testAdmin()
    {
        $this->assertForbidden(Request::create('/test/admin/admin'));
        $this->setAdministratorAccount();
        $this->assertResponseContains(Request::create('/test/admin/admin'), 'currentPathIsAdmin":true');
    }

    public function testNotAdmin()
    {
        $this->assertForbidden(Request::create('/test/admin/normal'));
        $this->setAdministratorAccount();
        $this->assertResponseContains(Request::create('/test/admin/normal'), 'currentPathIsAdmin":false');
    }

    public function testTemplate()
    {
        $this->setUpTemplate();
        $this->assertResponseContents(Request::create('/test/template/empty'), 'empty');
        $this->assertResponseContents(Request::create('/test/template/module-controller'), 'module-controller');
        $this->assertResponseContents(Request::create('/test/template/module-controller-action'), 'module-controller-action');
        $this->assertResponseContents(Request::create('/test/template/parameter'), 'value');
        $this->assertResponseContents(Request::create('/test/template/parameter-url/foo'), 'foo default');
        $this->assertResponseContents(Request::create('/test/template/streamable'), 'streamed');
        $this->assertResponseContents(Request::create('/test/template/vars/Monk'), 'Hello Monk');
    }

    private function setUpTemplate()
    {
        $sourceModule = $this->getDrupalRoot() . '/modules/controller_annotations/tests/modules/controller_annotations_test/templates/';

        if (!file_exists($sourceModule)) {
            $this->markTestSkipped('Test module can not be located');
        }

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

        // custom
        $this->setAnonymousAccount();
        $this->assertForbidden(Request::create('/test/security/custom'));
        $this->setAuthenticatedAccount();
        $this->assertForbidden(Request::create('/test/security/custom'));
        $this->setAdministratorAccount();
        $this->assertForbidden(Request::create('/test/security/custom'));
        $this->setAccount(new UserSession([
            'uid' => 1337
        ]));
        $this->assertResponseContents(Request::create('/test/security/custom'), 'OK');

        // custom inline
        $this->setAnonymousAccount();
        $this->assertForbidden(Request::create('/test/security/custom-inline'));
        $this->setAuthenticatedAccount();
        $this->assertForbidden(Request::create('/test/security/custom-inline'));
        $this->setAdministratorAccount();
        $this->assertForbidden(Request::create('/test/security/custom-inline'));
        $this->setAccount(new UserSession([
            'uid' => 1337
        ]));
        $this->assertResponseContents(Request::create('/test/security/custom-inline'), 'OK');

        // csrf
        $this->assertForbidden(Request::create('/test/security/csrf'));
        $this->assertResponseContents(Request::create('/test/security/csrf', 'GET', [
            'token' => $this->kernel->getContainer()->get('csrf_token')->get('test/security/csrf')
        ]), 'OK');
    }

    public function testTitle()
    {
        $this->assertTitleStartsWith(Request::create('/test/title/normal'), 'Hello World');
        $this->assertTitleStartsWith(Request::create('/test/title/arguments'), 'Hello MediaMonks');
        $this->assertTitleStartsWith(Request::create('/test/title/callback'), 'Hello Callback');
        $this->assertTitleStartsWith(Request::create('/test/title/callback-inline'), 'Hello Callback Inline');
    }

    public function testParamConverter()
    {
        $this->assertResponseContents(Request::create('/test/param-converter/date/2017-08-15'), '2017-08-15');
        $this->assertResponseContents(Request::create('/test/param-converter/date-format/15-08-2017'), '2017-08-15');
        $this->assertResponseContents(Request::create('/test/param-converter/date-multiple/14-08-2017/15-08-2017'), '2017-08-14-2017-08-15');
        $this->assertResponseContents(Request::create('/test/param-converter/date-optional/03-04-1985'), '1985-04-03');
        $this->assertResponseContents(Request::create('/test/param-converter/date-optional'), 'empty');
    }
}
