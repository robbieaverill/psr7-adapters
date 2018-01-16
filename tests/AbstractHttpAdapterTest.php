<?php

namespace Robbie\Psr7\Tests;

use PHPUnit_Framework_TestCase;
use Robbie\Psr7\AbstractHttpAdapter;

class AbstractHttpAdapterTest extends PHPUnit_Framework_TestCase
{
    public function testGetAndSetProtocolVersion()
    {
        /** @var AbstractHttpAdapter $mock */
        $mock = $this->getMockForAbstractClass(AbstractHttpAdapter::class);

        $mock->setProtocolVersion('1.0');
        $this->assertSame('1.0', $mock->getProtocolVersion());
    }
}
