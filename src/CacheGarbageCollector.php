<?php
/**
 * CacheGarbageCollector
 *
 * @category   CacheSerializer
 * @author     Gjero Krsteski <gjero@krsteski.de>
 */
class CacheGarbageCollector
{
    /**
     * @var CacheDba
     */
    protected $_cache;

    /**
     * @param CacheDba $cache
     */
    public function __construct(CacheDba $cache)
    {
        $this->_cache = $cache;
    }

    /**
     * Cleans the whole cache.
     * @return void
     */
    public function cleanAll()
    {
        $this->_process();
    }

    /**
     * Cleans the cache by expiration-time.
     * @param integer $seconds
     * @return void
     */
    public function cleanByExpiration($seconds = 300)
    {
        $this->_process(false, $seconds);
    }

    /**
     * The internal cleaning process.
     * @param boolean $cleanAll
     * @param integer $seconds
     * @return void
     */
    protected function _process($cleanAll = true, $seconds = 300)
    {
        $pointer = dba_firstkey($this->_cache->getDba());

        while($pointer)
        {
            if (true === $cleanAll)
            {
                $this->_cache->delete($pointer);
            }
            else
            {
                $this->_cache->get($pointer, $seconds);
            }

            $pointer = dba_nextkey($this->_cache->getDba());
        }

        dba_optimize($this->_cache->getDba());
    }
}