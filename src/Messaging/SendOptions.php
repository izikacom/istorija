<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Messaging;

class SendOptions
{
    const ENDPOINT_LOOPBACK = '$local$';

    private $destination;
    private $messageId;

    public function useCustomMessageId(string $messageId): self
    {
        $this->messageId = $messageId;

        return $this;
    }

    public function sendLocal(): self
    {
        $this->destination = self::ENDPOINT_LOOPBACK;

        return $this;
    }

    public function useEndpointLoopback(): bool
    {
        return self::ENDPOINT_LOOPBACK === $this->destination;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    public function getMessageId(): ?string
    {
        return $this->messageId;
    }
}
