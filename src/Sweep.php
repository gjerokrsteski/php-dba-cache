<?php

namespace PhpDbaCache;

/**
 * Cache Garbage Collector/Cleaner
 *
 * @category PhpDbaCache
 */
class Sweep
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Remove all cache entries.
     *
     * @return void
     */
    public function all()
    {
        $this->process();
    }

    /**
     * Remove too old cache entries.
     *
     * @return void
     */
    public function old()
    {
        $this->process(false);
    }

    /**
     * Internal cleaning process.
     *
     * @param boolean $cleanAll
     *
     * @return void
     */
    protected function process($cleanAll = true)
    {
        $key = dba_firstkey($this->cache->getDba());

        while ($key !== false && $key !== null) {
            if (true === $cleanAll) {
                $this->cache->delete($key);
            } else {
                $this->cache->get($key);
            }

            $key = dba_nextkey($this->cache->getDba());
        }

        dba_optimize($this->cache->getDba());
    }

    /**
     * Flush the whole storage.
     *
     * @throws \RuntimeException If can not flush file.
     * @return bool
     */
    public function flush()
    {
        $file = $this->cache->getStorage();

        if (file_exists($file)) {
            // close the dba file before delete
            // and reopen on next use
            $this->cache->closeDba();

            if (!@unlink($file)) {
                throw new \RuntimeException("can not flush file '$file'");
            }
        }

        return true;
    }
}
