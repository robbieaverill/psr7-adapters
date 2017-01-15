<?php

namespace Robbie\Psr7\Tests;

use Robbie\Psr7\HttpResponseAdapter;
use Psr\Http\Message\ResponseInterface;
use SilverStripe\Control\HTTPResponse;

/**
 * @package psr7-adapters
 */
class HttpResponseAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * {@inheritDoc}
     */
    protected $backupGlobals = false;

    /**
     * Ensure that the Response class returned is a PSR-7 interface
     */
    public function testReturnsPsr7CompliantClass()
    {
        $interface = $this->getInterface();

        $this->assertInstanceOf(ResponseInterface::class, $interface);
    }

    /**
     * Assert that the input can be retrieved again
     */
    public function testBodyMatchesInputBody()
    {
        $body = json_encode(['success' => true, 'message' => 'The operation was completed.']);
        $code = 202;
        $message = 'Yeah, accepted...';

        $interface = $this->getInterface($body, $code, $message);

        $this->assertSame($body, (string) $interface->getBody());
        $this->assertSame($code, $interface->getStatusCode());
        $this->assertSame($message, $interface->getReasonPhrase());
    }

    /**
     * Test that without providing a status code, the default is 200
     */
    public function testSuccessfulByDefault()
    {
        $interface = $this->getInterface();

        $this->assertSame(200, $interface->getStatusCode());
    }

    /**
     * Ensure that as per PSR-7 specifications the interfaces are immutable, and different instances returned
     * when we add headers to it (for example)
     */
    public function testImmutabilyAddHeaders()
    {
        $interface = $this->getInterface();

        $new = $interface->withHeader('Content-Type', 'application/json');
        $this->assertNotSame($new, $interface);
        $this->assertSame(['application/json'], $new->getHeader('Content-Type'));

        $new = $interface->withHeader('Custom-Header', 'value-here');
        $this->assertTrue($new->hasHeader('Custom-Header'));
        $this->assertFalse($interface->hasHeader('Custom-Header'));
    }

    /**
     * Test that a PSR-7 interface can be imported (with multiple headers converted to a single line) to a
     * HTTPResponse class
     */
    public function testCanImportPsr7InterfaceToHttpResponse()
    {
        $body = json_encode(['success' => true]);
        $interface = $this->getInterface($body, 201, 'Testing');
        $interface = $interface->withHeader('Foo', 'bar')->withAddedHeader('Foo', 'baz');

        $result = (new HttpResponseAdapter)->fromPsr7($interface);

        $this->assertSame($body, $result->getBody());
        $this->assertSame(201, $result->getStatusCode());
        $this->assertSame('Testing', $result->getStatusDescription());
        $this->assertSame('bar, baz', $result->getHeader('Foo'));
    }

    /**
     * Create a mocked HTTP response for "adapting"
     *
     * @param  string $body              The body of the response
     * @param  int    $statusCode        The numeric status code - 200, 404, etc
     * @param  string $statusDescription The text to be given alongside the status code
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function getInterface($body = null, $statusCode = null, $statusDescription = null)
    {
        $httpRequest = new HTTPResponse($body, $statusCode, $statusDescription);
        $adapter = new HttpResponseAdapter;
        return $adapter->toPsr7($httpRequest);
    }
}
