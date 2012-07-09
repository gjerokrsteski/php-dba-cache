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
   * Remove all cache entries.
   * @return void
   */
  public function cleanAll()
  {
    $this->_process();
  }

  /**
   * Remove cache entries by special expiration-time.
   * @param integer $seconds
   * @return void
   */
  public function cleanByExpiration($seconds = 300)
  {
    $this->_process(false, $seconds);
  }

  /**
   * Remove too old cache entries.
   * @return void
   */
  public function cleanOld()
  {
    $this->_process(false, microtime(true));
  }

  /**
   * Internal cleaning process.
   * @param boolean $cleanAll
   * @param integer $seconds
   * @return void
   */
  protected function _process($cleanAll = true, $seconds = 300)
  {
    $pointer = dba_firstkey($this->_cache->getDba());

    while ($pointer) {
      if (true === $cleanAll) {
        $this->_cache->delete($pointer);
      } else {
        $this->_cache->get($pointer, $seconds);
      }

      $pointer = dba_nextkey($this->_cache->getDba());
    }

    dba_optimize($this->_cache->getDba());
  }

  /**
   * Return the filling percentage of the backend storage
   * @throws RuntimeException If disk_total_space=0
   * @return int Integer between 0 and 100
   */
  public function getFillingPercentage()
  {
    clearstatcache();

    $dir   = dirname($this->_cache->getCacheFile());
    $free  = disk_free_space($dir);
    $total = disk_total_space($dir);

    if ($total == 0) {
      throw new RuntimeException('can\'t get disk_total_space');
    }

    if ($free >= $total) {
      return 100;
    }

    return ((int)(100. * ($total - $free) / $total));
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
