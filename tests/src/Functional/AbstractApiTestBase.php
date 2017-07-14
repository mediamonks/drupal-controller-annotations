<?php

namespace Drupal\Tests\controller_annotations\Functional;

use Drupal\Tests\BrowserTestBase;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiTestBase extends BrowserTestBase
{
    /**
     * @var array
     */
    public static $modules = ['controller_annotations', 'controller_annotations_test'];

    /**
     * @var Client $client
     */
    private $client;

    /**
     * @return Client
     */
    public function getClient()
    {
        if (empty($this->client)) {
            $this->client = $this->container->get('http_client_factory')->fromOptions([
                'timeout' => null,
                'verify' => false,
                'http_errors' => false
            ]);
        }

        return $this->client;
    }

    /**
     * @param string $method
     * @param string $path
     * @param string $contents
     */
    public function assertResponseContents($method, $path, $contents)
    {
        $response = $this->getClient()->request($method, $this->getAbsoluteUrl($path));
        $this->assertEquals($contents, $response->getBody()->getContents());
    }

    /**
     * @param string $method
     * @param string $path
     */
    public function assertNotFound($method, $path)
    {
        $response = $this->getClient()->request($method, $this->getAbsoluteUrl($path));
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @param string $method
     * @param string $path
     */
    public function assertMethodNotAllowed($method, $path)
    {
        $response = $this->getClient()->request($method, $this->getAbsoluteUrl($path));
        $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $response->getStatusCode());
    }
}
