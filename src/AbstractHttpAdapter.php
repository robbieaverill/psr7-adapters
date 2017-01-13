<?php

namespace Robbie\Psr7;

/**
 * Provides common functionality used between Request and Response objects
 *
 * @package psr7-adapters
 */
abstract class AbstractHttpAdapter
{
    /**
     * @var string
     */
    protected $protocolVersion;

    /**
     * Perform a conversion from a HTTPResponse or HTTPRequest into the corresponding PSR-7 interface
     *
     * @return \Psr\Http\Message\MessageInterface
     */
    abstract public function toPsr7();

    /**
     * Get the protocol version - either from a previously set value, or from the server
     *
     * @return string E.g. "1.1"
     */
    public function getProtocolVersion()
    {
        if ($this->protocolVersion) {
            return $this->protocolVersion;
        }

        $protocolAndVersion = $_SERVER['SERVER_PROTOCOL'];
        list($protocol, $version) = explode('/', $protocolAndVersion);
        return $version;
    }

    /**
     * Set the protocol version
     *
     * @param  string $version
     * @return $this
     */
    public function setProtocolVersion($version = '1.1')
    {
        $this->protocolVersion = $version;
        return $this;
    }
}
