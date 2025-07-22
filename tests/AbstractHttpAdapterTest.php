<?php

namespace Robbie\Psr7\Tests;

use PHPUnit\Framework\TestCase;
use Robbie\Psr7\AbstractHttpAdapter;

class AbstractHttpAdapterTest extends TestCase
{
    public function testGetAndSetProtocolVersion()
    {
        /** @var AbstractHttpAdapter $mock */
        $mock = $this->getMockForAbstractClass(AbstractHttpAdapter::class);

        $mock->setProtocolVersion('1.0');
        $this->assertSame('1.0', $mock->getProtocolVersion());
    }
}
