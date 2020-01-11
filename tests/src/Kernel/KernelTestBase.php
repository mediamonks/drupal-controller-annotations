<?php

namespace Drupal\Tests\controller_annotations\Kernel;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AnonymousUserSession;
use Drupal\KernelTests\KernelTestBase as BaseKernelTestBase;
use Drupal\Tests\controller_annotations\Kernel\TestUserSession as UserSession;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class KernelTestBase extends BaseKernelTestBase {

  /**
   * @var \Drupal\Core\DrupalKernel
   */
  protected $kernel;

  /**
   * @var array
   */
  public static $modules = ['controller_annotations', 'controller_annotations_test', 'user', 'system', 'node'];

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param $contents
   */
  protected function assertResponseContents(Request $request, $contents) {
    $response = $this->request($request);
    if ($response instanceof StreamedResponse) {
      ob_start();
      $response->sendContent();
      $actual = ob_get_contents();
      ob_end_clean();
    }
    else {
      $actual = $response->getContent();
    }

    $this->assertEquals($contents, trim($actual));
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param $contents
   */
  protected function assertResponseContains(Request $request, $contents) {
    $response = $this->request($request);
    $this->assertTrue(strpos($response->getContent(), $contents) !== FALSE);
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param $contents
   */
  protected function assertResponseNotContains(Request $request, $contents) {
    $response = $this->request($request);
    $this->assertTrue(strpos($response->getContent(), $contents) === FALSE);
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   */
  protected function assertNotFound(Request $request) {
    $response = $this->request($request);
    $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   */
  protected function assertMethodNotAllowed(Request $request) {
    $response = $this->request($request);
    $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $response->getStatusCode());
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   */
  protected function assertForbidden(Request $request) {
    $response = $this->request($request);
    $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   */
  protected function assertTitleStartsWith(Request $request, $title) {
    $this->assertResponseContains($request, '<title>' . $title);
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  protected function request(Request $request) {
    if (empty($this->kernel)) {
      $this->kernel = DrupalTestKernel::createFromRequest($request, $this->classLoader, 'prod');
    }

    return $this->kernel->handle($request);
  }

  /**
   *
   */
  protected function setAnonymousAccount() {
    $this->setAccount(new AnonymousUserSession());
  }

  /**
   *
   */
  protected function setAuthenticatedAccount() {
    $this->setAccount(new UserSession([
      'uid' => 2,
      'roles' => ['authenticated'],
    ]));
  }

  /**
   *
   */
  protected function setAdministratorAccount() {
    $this->setAccount(new UserSession([
      'uid' => 1,
      'roles' => ['administrator', 'authenticated'],
    ]));
  }

  /**
   * @param \Drupal\Core\Session\AccountInterface $account
   */
  protected function setAccount(AccountInterface $account) {
    $this->kernel->getContainer()->get('current_user')->setAccount($account);
  }

}
