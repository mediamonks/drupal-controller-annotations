<?php

namespace Drupal\Tests\controller_annotations\Kernel;

use Drupal\Core\DrupalKernel;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AnonymousUserSession;
use Drupal\Core\Session\UserSession;
use Drupal\KernelTests\KernelTestBase as BaseKernelTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

abstract class KernelTestBase extends BaseKernelTestBase
{
    /**
     * @var DrupalKernel
     */
    protected $kernel;

    /**
     * @var array
     */
    public static $modules = ['controller_annotations', 'controller_annotations_test', 'user'];

    /**
     * @param Request $request
     * @param $contents
     */
    protected function assertResponseContents(Request $request, $contents)
    {
        $response = $this->request($request);
        $this->assertEquals($contents, $response->getContent());
    }

    /**
     * @param Request $request
     */
    protected function assertNotFound(Request $request)
    {
        $response = $this->request($request);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @param Request $request
     */
    protected function assertMethodNotAllowed(Request $request)
    {
        $response = $this->request($request);
        $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $response->getStatusCode());
    }

    /**
     * @param Request $request
     */
    protected function assertForbidden(Request $request)
    {
        $response = $this->request($request);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * @param Request $request
     * @return Response
     */
    protected function request(Request $request)
    {
        if (empty($this->kernel)) {
            $this->kernel = DrupalTestKernel::createFromRequest($request, $this->classLoader, 'prod');
        }

        return $this->kernel->handle($request);
    }

    /**
     *
     */
    protected function setAnonymousAccount()
    {
        $this->setAccount(new AnonymousUserSession());
    }

    /**
     *
     */
    protected function setAdministratorAccount()
    {
        $this->setAccount(new UserSession([
            'uid' => 1,
            'roles' => ['administrator', 'authenticated']
        ]));
    }

    /**
     * @param AccountInterface $account
     */
    protected function setAccount(AccountInterface $account)
    {
        $this->kernel->getContainer()->get('current_user')->setAccount($account);
    }
}
