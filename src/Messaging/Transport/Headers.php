<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Messaging\Transport;

class Headers implements \Iterator, \Countable, \ArrayAccess, \JsonSerializable
{
    private $headers = [];

    const MIME_TYPE = 'Istorija.mimeType';
    const MESSAGE_ID = 'Istorija.messageId';
    const CORRELATION_ID = 'Istorija.correlationId';

    public function add(Header $header): self
    {
        if (array_key_exists($header->getName(), $this->headers)) {

//            throw new CannotOverwriteExistingHeader($header);
        }

        $this->headers[$header->getName()] = $header;

        return $this;
    }

    public function current(): Header
    {
        return current($this->headers);
    }

    public function next()
    {
        next($this->headers);
    }

    public function key()
    {
        return key($this->headers);
    }

    public function valid()
    {
        return (null !== key($this->headers));
    }

    public function rewind()
    {
        reset($this->headers);
    }

    public function offsetExists($offset)
    {
        return isset($this->headers[$offset]);
    }

    public function offsetGet($offset): Header
    {
        return $this->headers[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->add(new Header($offset, $value));
    }

    public function offsetUnset($offset)
    {
        throw new \RuntimeException('Cannot remove Header');
    }

    public function count(): int
    {
        return count($this->headers);
    }

    public function jsonSerialize()
    {
        return array_map(function (Header $header) {
            return $header->getValue();
        }, $this->headers);
    }
}
