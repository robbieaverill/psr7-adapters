<?php

namespace Robbie\Psr7;

use GuzzleHttp\Psr7\Response;
use SilverStripe\Control\HTTPResponse;

/**
 * @package psr7-adapters
 */
class HttpResponseAdapter extends AbstractHttpAdapter
{
    /**
     * {@inheritDoc}
     */
    public function toPsr7($input)
    {
        return new Response(
            $input->getStatusCode(),
            $input->getHeaders(),
            $input->getBody(),
            $this->getProtocolVersion(),
            $input->getStatusDescription()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function fromPsr7($input)
    {
        $adapted = new HTTPResponse(
            (string) $input->getBody(),
            $input->getStatusCode(),
            $input->getReasonPhrase()
        );

        $this->importHeaders($input, $adapted);

        return $adapted;
    }
}
