<?php

namespace DayUse\Istorija\EventStore\Storage\DoctrineDbal\MySql\Queries;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
final class InitStorage
{
    private $tableName;

    public function getSql()
    {
        $sql = <<<'MYSQL'
CREATE TABLE IF NOT EXISTS `%s` (
  `checkpointNumber` bigint(20) NOT NULL AUTO_INCREMENT,
  `canonicalStreamName` varchar(255) NOT NULL,
  `streamId` char(36) NOT NULL,
  `streamContract` varchar(255) NOT NULL,
  `eventId` char(36) NOT NULL,
  `eventContract` varchar(255) NOT NULL,
  `eventData` text NOT NULL,
  `eventDataContentType` text NOT NULL,
  `eventMetadata` text,
  `eventMetadataContentType` text,
  `commitId` char(36) NOT NULL,
  `utcCommittedTime` datetime NOT NULL,
  PRIMARY KEY (`checkpointNumber`),
  KEY `streamId` (`streamId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DELIMITER $$
CREATE FUNCTION ES_OPTIMISITIC_CONCURRENCY_FAILED() 
    RETURNS INT 
BEGIN 
    SIGNAL SQLSTATE '45001' SET MESSAGE_TEXT = 'Optimistic concurrency failed, wrong expected version';
    RETURN NULL; 
END;
$$
MYSQL;

        return sprintf($sql, $this->tableName);
    }

    public static function table(?string $name = null)
    {
        return new self($name);
    }

    private function __construct(?string $name = null)
    {
        $this->tableName = $name ?? Defaults::TABLE_NAME;
    }
}
