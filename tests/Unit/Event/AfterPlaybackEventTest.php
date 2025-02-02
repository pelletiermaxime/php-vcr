<?php

namespace VCR\Tests\Unit\Event;

use PHPUnit\Framework\TestCase;
use VCR\Cassette;
use VCR\Configuration;
use VCR\Event\AfterPlaybackEvent;
use VCR\Request;
use VCR\Response;
use VCR\Storage;

class AfterPlaybackEventTest extends TestCase
{
    /**
     * @var AfterPlaybackEvent
     */
    private $event;

    protected function setUp(): void
    {
        $this->event = new AfterPlaybackEvent(
            new Request('GET', 'http://example.com'),
            new Response('200'),
            new Cassette('test', new Configuration(), new Storage\Blackhole())
        );
    }

    public function testGetRequest(): void
    {
        $this->assertInstanceOf('VCR\Request', $this->event->getRequest());
    }

    public function testGetResponse(): void
    {
        $this->assertInstanceOf('VCR\Response', $this->event->getResponse());
    }

    public function testGetCassette(): void
    {
        $this->assertInstanceOf('VCR\Cassette', $this->event->getCassette());
    }
}
