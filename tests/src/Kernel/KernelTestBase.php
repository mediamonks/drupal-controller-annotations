<?php

namespace Drupal\Tests\controller_annotations\Kernel;

use Drupal\Core\DrupalKernel;
use Drupal\KernelTests\KernelTestBase as BaseKernelTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class KernelTestBase extends BaseKernelTestBase
{
    /**
     * @var DrupalKernel
     */
    protected $kernel;

    /**
     * @var array
     */
    public static $modules = ['controller_annotations', 'controller_annotations_test'];

    /**
     * @param Request $request
     * @param $contents
     */
    public function assertResponseContents(Request $request, $contents)
    {
        $response = $this->request($request);
        $this->assertEquals($contents, $response->getContent());
    }

    /**
     * @param Request $request
     */
    public function assertNotFound(Request $request)
    {
        $response = $this->request($request);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @param Request $request
     */
    public function assertMethodNotAllowed(Request $request)
    {
        $response = $this->request($request);
        $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $response->getStatusCode());
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
}
