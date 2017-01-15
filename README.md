# robbie/psr7-adapters

[![Build Status](https://travis-ci.org/robbieaverill/psr7-adapters.svg?branch=master)](https://travis-ci.org/robbieaverill/psr7-adapters) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/robbieaverill/psr7-adapters/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/robbieaverill/psr7-adapters/?branch=master) [![codecov](https://codecov.io/gh/robbieaverill/psr7-adapters/branch/master/graph/badge.svg)](https://codecov.io/gh/robbieaverill/psr7-adapters)


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

### Converting to PSR-7

This module works by providing either a `HTTPRequest` or a `HTTPResponse` class that is pre-configured and ready to be
sent to the client/server to the corresponding adapter class:

* `HTTPRequest` uses the `Robbie\Psr7\HttpRequestAdapter` class
* `HTTPResponse` uses the `Robbie\Psr7\HttpResponseAdapter` class

To retrieve a bootstrapped PSR-7 `ServerRequestInterface` or `ResponseInterface` you can call `->toPsr7($request)` on either of
these classes, for example:

```php
<?php

$myResponse = new \SilverStripe\Control\HTTPResponse(
    json_encode(['success' => true, 'message' => 'Your request was successful!']),
    200,
    'OK'
);

/** @var \Psr\Http\Message\ResponseInterface $response */
$response = (new \Robbie\Psr7\HttpResponseAdapter)->toPsr7($myResponse);
```

From here you can use any of the PSR-7 interface methods, and the results will always be immutable:

```php
<?php

$newResponse = $response->withHeader('Content-Type', 'application/json');
$newResponse = $newResponse->withHeader('X-Custom-Header', 'my-value-here');

// $response !== $newResponse -> #psr7-ftw
```

The same concept applies to the `HttpRequestAdapter`, for example:

```php
<?php

# Context: PageController
use Robbie\Psr7\HttpRequestAdapter;

// ...

$request = $this->getRequest();
$adapter = new HttpRequestAdapter;
$psrInterface = $adapter->toPsr7($request);

// Outputs all your initial request headers:
print_r($psrInterface->getHeaders());
```

### Converting from PSR-7

To return a PSR-7 interface back to either an `HTTPRequest` or `HTTPResponse` class you simply need to
do the same thing as going *to*, only use `->fromPsr7($input)` instead:

```php
<?php

// $requestInterface is an instance of Psr\Http\Message\ServerRequestInterface
$httpRequest = (new HttpRequestAdapter)->fromPsr7($requestInterface);
```

`$httpRequest` is not a SilverStripe `HTTPRequest` class.
