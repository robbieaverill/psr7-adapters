# robbie/psr7-adapters

[![Build Status](https://travis-ci.org/robbieaverill/psr7-adapters.svg?branch=master)](https://travis-ci.org/robbieaverill/psr7-adapters)

PSR-7 compliant, immutable adapter interfaces for SilverStripe HTTP classes.

## Requirements

* `silverstripe/framework` ^4.0
* `guzzlehttp/psr7`

## Installation

Install with [Composer](https://getcomposer.org):

```shell
composer require robbie/psr7-adapters
```

Add `?flush=1` to your browser URL, `flush=1` to your `sake` command arguments or `--flush` to your [`ssconsole`](https://github.com/silverleague/silverstripe-console).

## Use

This module works by providing either a `HTTPRequest` or a `HTTPResponse` class that is pre-configured and ready to be
sent to the client/server to the corresponding adapter class:

* `HTTPRequest` uses the `Robbie\Psr7\HttpRequestAdapter` class
* `HTTPResponse` uses the `Robbie\Psr7\HttpResponseAdapter` class

To retrieve a bootstrapped PSR-7 `ServerRequestInterface` or `ResponseInterface` you can call `->toPsr7()` on either of
these classes, for example:

```php
<?php

$myResponse = new \SilverStripe\Control\HTTPResponse(
    json_encode(['success' => true, 'message' => 'Your request was successful!']),
    200,
    'OK'
);

/** @var \Psr\Http\Message\ResponseInterface $response */
$response = $myResponse->toPsr7();
```

From here you can use any of the PSR-7 interface methods, and the results will always be immutable:

```php
$newResponse = $response->withHeader('Content-Type', 'application/json');
$newResponse = $newResponse->withHeader('X-Custom-Header', 'my-value-here');

// $response !== $newResponse
```

The same concept applies to the `HttpRequestAdapter`, for example:

```php
# Context: PageController
use Robbie\Psr7\HttpRequestAdapter;

// ...

$request = $this->getRequest();
$adapter = new HttpRequestAdapter($request);
$psrInterface = $adapter->toPsr7();

print_r($psrInterface->getHeaders());
```
