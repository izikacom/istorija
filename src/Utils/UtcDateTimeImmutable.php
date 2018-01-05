<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Utils;

class UtcDateTimeImmutable extends \DateTimeImmutable
{
    public function __construct(string $time = 'now', \DateTimeZone $timezone = null)
    {
        parent::__construct($time, new \DateTimeZone('UTC'));
    }

    public static function createFromFormat($format, $time, $timezone = null)
    {
        $dateTimeImmutable = parent::createFromFormat($format, $time, new \DateTimeZone('UTC'));

        return $dateTimeImmutable ? new self($dateTimeImmutable->format(\DateTime::ATOM)) : $dateTimeImmutable;
    }

    public static function createFromMutable($dateTime): self
    {
        $dateTimeImmutable = parent::createFromMutable($dateTime);
        $dateTimeImmutable->setTimezone(new \DateTimeZone('UTC'));

        return new self($dateTimeImmutable->format(\DateTime::ATOM));
    }

    public function __toString()
    {
        return $this->format('Y-m-d\TH:i:s\Z');
    }
}
