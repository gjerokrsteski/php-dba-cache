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
class Sweeper
{
  /**
   * @var Cache
   */
  protected $_cache;

  /**
   * @param Cache $cache
   */
  public function __construct(Cache $cache)
  {
    $this->_cache = $cache;
  }

  /**
   * Remove all cache entries.
   * @return void
   */
  public function cleanAll()
  {
    $this->_process();
  }

  /**
   * Remove too old cache entries.
   * @return void
   */
  public function cleanOld()
  {
    $this->_process(false);
  }

  /**
   * Internal cleaning process.
   * @param boolean $cleanAll
   * @return void
   */
  protected function _process($cleanAll = true)
  {
    $pointer = dba_firstkey($this->_cache->getDba());

    while ($pointer) {
      if (true === $cleanAll) {
        $this->_cache->delete($pointer);
      } else {
        $this->_cache->get($pointer);
      }

      $pointer = dba_nextkey($this->_cache->getDba());
    }

    dba_optimize($this->_cache->getDba());
  }

  /**
   * Flush the whole storage.
   * @throws RuntimeException If can not flush file.
   * @return bool
   */
  public function flush()
  {
    $cacheFile = $this->_cache->getCacheFile();

    if (file_exists($cacheFile)) {

      // close the dba file before delete
      // and reopen on next use
      $this->_cache->closeDba();

      if (!unlink($cacheFile)) {
        throw new RuntimeException("unlink('{$cacheFile}') failed");
      }
    }

    return true;
  }
}
