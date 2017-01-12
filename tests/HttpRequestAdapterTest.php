<?php

namespace Robbie\Psr7\Tests;

use Robbie\Psr7\HttpRequestAdapter;
use Psr\Http\Message\ServerRequestInterface;
use SilverStripe\Control\HTTPRequest;

/**
 * @package psr7-adapters
 */
class HttpRequestAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ensure that the Request class returned is a PSR-7 interface
     */
    public function testReturnsPsr7CompliantClass()
    {
        $interface = $this->getInterface('GET', '/path/to/request');

        $this->assertInstanceOf(ServerRequestInterface::class, $interface);
    }

    /**
     * Ensure that the returned interface is immutable as per PSR-7 specs
     */
    public function testGetImmutableInterfaceAfterChangingQueryParams()
    {
        $original = $this->getInterface('GET', '/path/to/request', ['query' => 'string', 'values' => 'here']);
        $new = $original->withQueryParams(['foo' => 'bar']);

        $this->assertNotSame($original, $new);
        $this->assertSame('here', $original->getQueryParams()['values']);
        $this->assertArrayNotHasKey('bar', $original->getQueryParams());
        $this->assertArrayNotHasKey('here', $new->getQueryParams());
        $this->assertSame('bar', $new->getQueryParams()['foo']);
    }

    /**
     * Test that POSTed data from the request body can be returned
     */
    public function testPostedDataCanBeReturned()
    {

        $interface = $this->getInterface('POST', '/path/to/post', [], ['foo' => 'bar'], 'foo=bar');

        $this->assertSame('foo=bar', (string) $interface->getBody());
    }

    /**
     * @param  string $method
     * @param  string $uri
     * @param  array  $get
     * @param  array  $post
     * @param  string $body
     * @return ServerRequestInterface
     */
    protected function getInterface($method, $uri, $get = [], $post = [], $body = null)
    {
        $httpRequest = new HTTPRequest($method, $uri, $get, $post, $body);
        $adapter = new HttpRequestAdapter($httpRequest);
        $adapter->setServerVars($this->mockRequestData());
        return $adapter->toPsr7();
    }

    /**
     * @return array
     */
    protected function mockRequestData()
    {
        return [
            'SERVER_PORT'   => 80,
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW'   => 'bar',
            'HTTP_HOST'     => 'example.com',
            'REQUEST_URI'   => '/path/to/request?query=string&values=here'
        ];
    }
}
