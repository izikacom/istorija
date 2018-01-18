<?php

namespace Dayuse\Istorija\EventSourcing\DomainEvent;

class EventDateTime
{
    private const FORMAT_STRING = 'Y-m-d\TH:i:s.uP';

    /**
     * @var \DateTimeInterface
     */
    private static $mockedNow;

    /**
     * @var \DateTimeImmutable
     */
    private $dateTime;

    private function __construct(\DateTimeImmutable $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public static function now(): self
    {
        if (static::$mockedNow) {
            return new self(\DateTimeImmutable::createFromFormat(
                self::FORMAT_STRING,
                static::$mockedNow->format(self::FORMAT_STRING)
            ));
        }

        return new self(
            \DateTimeImmutable::createFromFormat(
                'U.u',
                sprintf('%.6F', microtime(true)),
                new \DateTimeZone('UTC')
            )
        );
    }

    public function toDateTimeImmutable(): \DateTimeImmutable
    {
        return new \DateTimeImmutable((string) $this);
    }

    public function __toString(): string
    {
        return $this->dateTime->format(self::FORMAT_STRING);
    }

    public static function fromString(string $value): self
    {
        return new self(\DateTimeImmutable::createFromFormat(self::FORMAT_STRING, $value));
    }

    public function equals(self $dateTime): bool
    {
        return $this->__toString() === $dateTime->__toString();
    }

    public static function mockNow(\DateTimeInterface $dateTime): void
    {
        static::$mockedNow = $dateTime;
    }
}
