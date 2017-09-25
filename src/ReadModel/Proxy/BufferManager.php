<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 29/03/2017
 * Time: 11:03
 */

namespace DayUse\Istorija\ReadModel\Proxy;

use DayUse\Istorija\Utils\Ensure;

class BufferManager
{
    /**
     * @var Buffer[]
     */
    private $bufferList;

    /**
     * @var boolean
     */
    private $enabled;

    /**
     * BufferedDAOManager constructor.
     *
     * @param Buffer[] $bufferList
     */
    public function __construct(array $bufferList = [])
    {
        Ensure::allIsInstanceOf($bufferList, Buffer::class);

        $this->bufferList = $bufferList;
        $this->enabled    = false;
    }

    public function addBufferedDAO(Buffer $bufferedDAO)
    {
        if ($this->enabled) {
            $bufferedDAO->enable();
        }

        $this->bufferList[] = $bufferedDAO;
    }

    public function flushAndCommit()
    {
        foreach ($this->bufferList as $dao) {
            $dao->flushAndCommit();
        }
    }

    public function commit()
    {
        foreach ($this->bufferList as $dao) {
            $dao->commit();
        }
    }

    public function enableBuffering()
    {
        $this->enabled = true;
        foreach ($this->bufferList as $dao) {
            $dao->enable();
        }
    }
}
