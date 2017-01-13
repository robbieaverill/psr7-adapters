<?php

namespace Robbie\Psr7;

use GuzzleHttp\Psr7\Response;
use Robbie\Psr7\AbstractHttpAdapter;
use SilverStripe\Control\HTTPResponse;

/**
 * @package psr7-adapters
 */
class HttpResponseAdapter extends AbstractHttpAdapter
{
    /**
     * @var HTTPResponse
     */
    protected $httpResponse;

    /**
     * Creates a PSR-7 compliant ResponseInterface from the given HTTPResponse
     *
     * @param HTTPResponse $request
     */
    public function __construct(HTTPResponse $request)
    {
        $this->httpResponse = $request;
    }

    /**
     * Return a bootstrapped PSR-7 ResponseInterface
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function toPsr7()
    {
        return new Response(
            $this->httpResponse->getStatusCode(),
            $this->httpResponse->getHeaders(),
            $this->httpResponse->getBody(),
            $this->getProtocolVersion(),
            $this->httpResponse->getStatusDescription()
        );
    }
}
