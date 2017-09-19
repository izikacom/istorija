<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\EventStore;

class EventData
{
    private $payload;
    private $contentType;

    public function __construct(string $payload, string $contentType)
    {
        $this->payload = $payload;
        $this->contentType = $contentType;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function __toString()
    {
        return $this->payload;
    }
}
