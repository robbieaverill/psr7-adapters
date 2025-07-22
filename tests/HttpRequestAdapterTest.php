<?php

namespace Robbie\Psr7\Tests;

use PHPUnit\Framework\TestCase;
use Robbie\Psr7\HttpRequestAdapter;
use Psr\Http\Message\ServerRequestInterface;
use SilverStripe\Control\HTTPRequest;

/**
 * @package psr7-adapters
 */
class HttpRequestAdapterTest extends TestCase
{
    /**
     * {@inheritDoc}
     */
    protected $backupGlobals = false;

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
     * Test that a PSR-7 interface can be imported into a HTTPRequest interface
     */
    public function testGetHttpRequestFromPsr7Interface()
    {
        $interface = $this->getInterface('POST', '/path/to/post', ['abc' => 'def'], ['foo' => 'bar'], 'foo=bar');
        $interface = $interface->withHeader('Token', 'foo')->withAddedHeader('Token', 'bar');

        $result = (new HTTPRequestAdapter)->fromPsr7($interface);

        $this->assertSame('POST', $result->httpMethod());
        $this->assertSame('path/to/post', $result->getURL());
        $this->assertSame(['abc' => 'def'], $result->getVars());
        $this->assertSame(['foo' => 'bar'], $result->postVars());
        $this->assertSame('foo, bar', $result->getHeader('Token'));
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
        // set server protocol as AbstractHttpAdapter relies on it
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';

        $httpRequest = new HTTPRequest($method, $uri, $get, $post, $body);
        $adapter = new HttpRequestAdapter;
        $adapter->setServerVars($this->mockRequestData());
        return $adapter->toPsr7($httpRequest);
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
