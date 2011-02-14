<?php
/**
* CacheGarbageCollector
*
* LICENSE
*
* This source file is subject to the new BSD license that is bundled
* with this package in the file LICENSE.
* It is also available through the world-wide-web at this URL:
* http://krsteski.de/new-bsd-license/
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to gjero@krsteski.de so we can send you a copy immediately.
*
* @category  CacheDba
* @copyright Copyright (c) 2010-2011 Gjero Krsteski (http://krsteski.de)
* @license   http://krsteski.de/new-bsd-license New BSD License
*/

/**
 * CacheGarbageCollector
 *
 * @category  CacheDba
 * @copyright Copyright (c) 2010-2011 Gjero Krsteski (http://krsteski.de)
 * @license   http://krsteski.de/new-bsd-license New BSD License
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