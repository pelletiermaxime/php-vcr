<?php

namespace VCR\Tests\Unit\Event;

use PHPUnit\Framework\TestCase;
use VCR\Event\BeforeHttpRequestEvent;
use VCR\Request;

class BeforeHttpRequestEventTest extends TestCase
{
    /**
     * @var BeforeHttpRequestEvent
     */
    private $event;

    protected function setUp(): void
    {
        $this->event = new BeforeHttpRequestEvent(new Request('GET', 'http://example.com'));
    }

    public function testGetRequest(): void
    {
        $this->assertInstanceOf('VCR\Request', $this->event->getRequest());
    }
}
