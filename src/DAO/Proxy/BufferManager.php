<?php
namespace Dayuse\Istorija\DAO\Proxy;

use Dayuse\Istorija\Utils\Ensure;

class BufferManager
{
    /** @var Buffer[] */
    private $bufferList;
    private $enabled;

    public function __construct(iterable $bufferList = [])
    {
        Ensure::allIsInstanceOf($bufferList, Buffer::class);

        $this->bufferList = $bufferList;
        $this->enabled    = false;
    }

    public function addBufferedDAO(Buffer $bufferedDAO): void
    {
        if ($this->enabled) {
            $bufferedDAO->enable();
        }

        $this->bufferList[] = $bufferedDAO;
    }

    public function flushAndCommit(): void
    {
        foreach ($this->bufferList as $dao) {
            $dao->flushAndCommit();
        }
    }

    public function commit(): void
    {
        foreach ($this->bufferList as $dao) {
            $dao->commit();
        }
    }

    public function enableBuffering(): void
    {
        $this->enabled = true;
        foreach ($this->bufferList as $dao) {
            $dao->enable();
        }
    }
}
