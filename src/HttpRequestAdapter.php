<?php

namespace Robbie\Psr7;

use GuzzleHttp\Psr7\ServerRequest;
use Robbie\Psr7\AbstractHttpAdapter;
use SilverStripe\Control\HTTPRequest;

/**
 * @package psr7-adapters
 */
class HttpRequestAdapter extends AbstractHttpAdapter
{
    /**
     * @var array
     */
    protected $serverVars;

    /**
     * @var HTTPRequest
     */
    protected $httpRequest;

    /**
     * Creates a PSR-7 compliant ServerRequestInterface from the given HTTPRequest
     *
     * @param HTTPRequest $request
     */
    public function __construct(HTTPRequest $request)
    {
        $this->httpRequest = $request;
        $this->serverVars = $_SERVER;
    }

    /**
     * Return a bootstrapped PSR-7 ServerRequestInterface
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function toPsr7()
    {
        $request = new ServerRequest(
            $this->httpRequest->httpMethod(),
            $this->getUri(),
            $this->httpRequest->getHeaders(),
            $this->httpRequest->getBody(),
            $this->getProtocolVersion(),
            $_SERVER
        );

        if (!empty($this->httpRequest->getVars())) {
            $request = $request->withQueryParams($this->httpRequest->getVars());
        }

        if (!empty($this->httpRequest->postVars())) {
            $request = $request->withParsedBody($this->httpRequest->postVars());
        }

        return $request;
    }

    /**
     * Get the full request URI (can be empty, but probably won't be)
     *
     * @return string
     */
    public function getUri()
    {
        $vars = $this->getServerVars();

        $uri = '';
        $protocol = (isset($vars['HTTPS']) || $vars['SERVER_PORT'] === '443') ? 'https' : 'http';
        $uri .= $protocol . '://';

        if (!empty($vars['PHP_AUTH_USER'])) {
            $uri .= $vars['PHP_AUTH_USER'];

            if (!empty($vars['PHP_AUTH_PW'])) {
                $uri .= ':' . $vars['PHP_AUTH_PW'];
            }

            $uri .= '@';
        }

        if (!empty($vars['HTTP_HOST'])) {
            $uri .= $vars['HTTP_HOST'];
        }

        $uri .= $vars['REQUEST_URI'];

        return $uri;
    }

    /**
     * @return array
     */
    public function getServerVars()
    {
        return $this->serverVars;
    }

    /**
     * @param  array $vars
     * @return $this
     */
    public function setServerVars(array $vars)
    {
        $this->serverVars = $vars;
        return $this;
    }
}
