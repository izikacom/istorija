<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\EventStore\Storage\DoctrineDbal\MySql\Queries;

use Dayuse\Istorija\EventStore\CommitId;
use Dayuse\Istorija\EventStore\Stream;
use Dayuse\Istorija\EventStore\StreamName;
use Dayuse\Istorija\EventStore\UncommittedEvent;
use Dayuse\Istorija\Utils\Ensure;

class PersistUncommitedEvent
{
    private $stream;
    private $uncommittedEvent;
    private $commitId;

    public function __construct(UncommittedEvent $event, StreamName $toStream, CommitId $commitId)
    {
        $this->uncommittedEvent = $event;
        $this->stream = $toStream;
        $this->commitId = $commitId;
    }

    public function getSql(): string
    {
        $sql =<<<'SQL'
INSERT INTO `%s` (
  `checkpointNumber`, 
  `canonicalStreamName`,
  `streamId`, 
  `streamContract`, 
  `eventId`, 
  `eventContract`, 
  `eventData`, 
  `eventDataContentType`, 
  `eventMetadata`, 
  `eventMetadataContentType`, 
  `commitId`, 
  `utcCommittedTime`
) 
VALUES 
(
  NULL,
  :canonicalStreamName, 
  :streamId, 
  :streamContract, 
  :eventId,
  :eventContract,
  :eventData,
  :eventDataContentType,
  :eventMetadata,
  :eventMetadataContentType,
  :commitId,
  NOW()
)
;
SQL;

        return sprintf($sql, Defaults::TABLE_NAME);
    }

    public function getParameters(): array
    {
        return [
            'canonicalStreamName'  => (string) $this->stream->getCanonicalStreamName(),
            'streamId'             => (string) $this->stream->getIdentifier(),
            'streamContract'       => (string) $this->stream->getContract(),
            'eventId'              => (string) $this->uncommittedEvent->getEventId(),
            'eventContract'        => (string) $this->uncommittedEvent->getContract(),
            'eventData'            => $this->uncommittedEvent->getData()->getPayload(),
            'eventDataContentType' => $this->uncommittedEvent->getData()->getContentType(),
            'eventMetadata'        => $this->uncommittedEvent->getMetadata()
                ? $this->uncommittedEvent->getMetadata()->getPayload()
                : null,
            'eventMetadataContentType' => $this->uncommittedEvent->getMetadata()
                ? $this->uncommittedEvent->getMetadata()->getContentType()
                : null,
            'commitId' => (string) $this->commitId,
        ];
    }
}
