<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Utils;

class JsonPayloadDescription
{
    private $payloadName;
    private $payloadProperties;

    public function __construct(string $payloadName, array $payloadProperties = [])
    {
        $this->payloadName = $payloadName;
        $this->payloadProperties = $payloadProperties;
    }

    public function getPayloadName(): string
    {
        return $this->payloadName;
    }

    public function getPayloadProperties(): array
    {
        return $this->payloadProperties;
    }
}
