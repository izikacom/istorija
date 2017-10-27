<?php

namespace Dayuse\Istorija\EventStore\Storage\DoctrineDbal;

use Dayuse\Istorija\EventStore\AdvancedReadQuery;
use Dayuse\Istorija\EventStore\AdvancedStorage;
use Dayuse\Istorija\EventStore\AllEventsReadResult;
use Dayuse\Istorija\EventStore\AllEventsReadResultUsingGenerator;
use Dayuse\Istorija\EventStore\EventRecord;
use Dayuse\Istorija\EventStore\EventRecordNotFound;
use Dayuse\Istorija\EventStore\ExpectedVersion;
use Dayuse\Istorija\EventStore\SlicedReadResult;
use Dayuse\Istorija\EventStore\SlicedReadResultUsingGenerator;
use Dayuse\Istorija\EventStore\Storage;
use Dayuse\Istorija\EventStore\Storage\DoctrineDbal\MySql\Queries\CheckExpectedVersion;
use Dayuse\Istorija\EventStore\Storage\DoctrineDbal\MySql\Queries\DeleteStream;
use Dayuse\Istorija\EventStore\Storage\DoctrineDbal\MySql\Queries\InitStorage;
use Dayuse\Istorija\EventStore\Storage\DoctrineDbal\MySql\Queries\PersistUncommitedEvent;
use Dayuse\Istorija\EventStore\Storage\DoctrineDbal\MySql\Queries\ReadAllEvents;
use Dayuse\Istorija\EventStore\Storage\DoctrineDbal\MySql\Queries\ReadEvent;
use Dayuse\Istorija\EventStore\Storage\DoctrineDbal\MySql\Queries\ReadStreamEvents;
use Dayuse\Istorija\EventStore\Storage\DoctrineDbal\MySql\Queries\SelectAndSetCurrentStreamVersion;
use Dayuse\Istorija\EventStore\CommitId;
use Dayuse\Istorija\EventStore\StreamName;
use Dayuse\Istorija\Utils\NotImplemented;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\DriverException;
use Dayuse\Istorija\EventStore\Storage\OptimisticConcurrencyFailed;
use Dayuse\Istorija\EventStore\Storage\RequiresInitialization;
use function Dayuse\Istorija\EventStore\Storage\DoctrineDbal\MySql\hydrateFromRow;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
class MySqlDbalStorage implements Storage, AdvancedStorage, RequiresInitialization
{
    private $dbal;

    public function __construct(Connection $dbal)
    {
        $this->dbal = $dbal;
    }

    public function initialize(): void
    {
        $query = InitStorage::table();
        $this->dbal->executeQuery($query->getSql());
    }

    public function persist(StreamName $toStream, array $uncommittedEvents, ?int $expectedVersion): void
    {
        $this->dbal->beginTransaction();

        $this->checkExpectedVersionInCurrentTransaction($toStream, $expectedVersion);

        $commitId = CommitId::generate();

        foreach ($uncommittedEvents as $uncommittedEvent) {
            $persistQuery = new PersistUncommitedEvent($uncommittedEvent, $toStream, $commitId);
            $stmt = $this->dbal->prepare($persistQuery->getSql());
            $stmt->execute($persistQuery->getParameters());
        }

        $this->dbal->commit();
    }

    public function delete(StreamName $stream, int $expectedVersion): void
    {
        $this->dbal->beginTransaction();

        $this->checkExpectedVersionInCurrentTransaction($stream, $expectedVersion);

        $deleteQuery = new DeleteStream($stream);
        $stmt = $this->dbal->prepare($deleteQuery->getSql());
        $stmt->execute($deleteQuery->getParameters());

        $this->dbal->commit();
    }

    public function readEvent(StreamName $stream, int $eventNumber): EventRecord
    {
        $readEventQuery = ReadEvent::fromStream($stream, $eventNumber);
        $stmt = $this->dbal->prepare($readEventQuery->getSql());
        $stmt->execute($readEventQuery->getParameters());

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);


        if (false === $row) {

            throw EventRecordNotFound::onStream($stream, $eventNumber);
        }

        return hydrateFromRow($row, $eventNumber);
    }

    public function readStreamEvents(StreamName $stream, int $start = 0, int $count = PHP_INT_MAX): SlicedReadResult
    {
        $query = ReadStreamEvents::forward($stream, $start, $count);

        $stmt = $this->dbal->prepare($query->getSql());
        $stmt->execute($query->getParameters());

        $generatorLambda = function() use ($stmt, $start) {
            $eventNumber = $start;
            while (false !== ($row = $stmt->fetch(\PDO::FETCH_ASSOC))) {

                yield hydrateFromRow($row, $eventNumber++);
            }
        };

        return new SlicedReadResultUsingGenerator($stream, $generatorLambda, $start, $count);
    }

    public function readAllEvents(int $start = 0, int $count = PHP_INT_MAX): AllEventsReadResult
    {
        $query = ReadAllEvents::forward($start, $count);

        $stmt = $this->dbal->prepare($query->getSql());
        $stmt->execute($query->getParameters());

        $generatorLambda = function() use ($stmt, $start) {
            $eventNumber = $start;
            while (false !== ($row = $stmt->fetch(\PDO::FETCH_ASSOC))) {

                yield hydrateFromRow($row, $eventNumber++);
            }
        };

        return new AllEventsReadResultUsingGenerator($generatorLambda, $start, $count);
    }

    public function supportsAdvancedReadQuery(AdvancedReadQuery $query)
    {
        throw NotImplemented::feature('Advanced Read Query feature is not available yet.');
    }

    public function readUsingAdvancedQuery(AdvancedReadQuery $query)
    {
        throw NotImplemented::feature('Advanced Read Query feature is not available yet.');
    }

    private function checkExpectedVersionInCurrentTransaction(StreamName $stream, int $expectedVersion)
    {
        if (!$this->dbal->isTransactionActive()) {

            throw new \LogicException('No active transaction');
        }

        $currentStreamVersionQuery = new SelectAndSetCurrentStreamVersion($stream);

        $stmt = $this->dbal->prepare($currentStreamVersionQuery->getSql());
        $stmt->execute($currentStreamVersionQuery->getParameters());
        $currentStreamVersion = (int) $stmt->fetchColumn(0);

        if (ExpectedVersion::ANY !== $expectedVersion) {
            $checkExpectedVersionQuery = new CheckExpectedVersion($expectedVersion);
            $stmt = $this->dbal->prepare($checkExpectedVersionQuery->getSql());
            try {
                $stmt->execute($checkExpectedVersionQuery->getParameters());
            } catch (DriverException $e) {
                if ('45001' === $e->getSQLState()) {

                    throw OptimisticConcurrencyFailed::versionDoesNotMatch($expectedVersion, $currentStreamVersion);
                }

                throw $e;
            }
        }
    }
}
