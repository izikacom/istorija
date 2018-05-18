<?php
namespace Dayuse\Istorija\EventStore;

use Dayuse\Istorija\Identifiers\UuidIdentifier;
use Ramsey\Uuid\Codec\TimestampFirstCombCodec;
use Ramsey\Uuid\Generator\CombGenerator;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidFactoryInterface;

final class EventId extends UuidIdentifier
{
    /** @var UuidFactoryInterface */
    private static $uuidFactory;

    public static function generate()
    {
        return static::fromString(self::getUuidFactory()->uuid4());
    }

    private static function getUuidFactory(): UuidFactoryInterface
    {
        if(static::$uuidFactory) {
            return static::$uuidFactory;
        }

        $uuidFactory = new UuidFactory();

        $uuidFactory->setRandomGenerator(new CombGenerator(
            $uuidFactory->getRandomGenerator(),
            $uuidFactory->getNumberConverter()
        ));

        $uuidFactory->setCodec(new TimestampFirstCombCodec(
            $uuidFactory->getUuidBuilder()
        ));

        static::$uuidFactory = $uuidFactory;

        return static::$uuidFactory;
    }
}
