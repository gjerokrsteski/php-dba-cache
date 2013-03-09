<?php
/**
 * CacheDba - This class provides the functionality required to store
 * and retrieve PHP objects, strings, integers or arrays.
 * It uses the database (dbm-style) abstraction layer for persistence.
 * Even instances of SimpleXMLElement can be stored. You don't have
 * to matter about the size of the cache-file. It depends on the free
 * space of your disk.
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
 * CacheDba
 *
 * @category  CacheDba
 * @copyright Copyright (c) 2010-2011 Gjero Krsteski (http://krsteski.de)
 * @license   http://krsteski.de/new-bsd-license New BSD License
 */
class Cache
{
  /**
   * @var resource
   */
  protected $_dba;

  /**
   * @var string
   */
  protected $_handler;

  /**
   * @var string
   */
  protected $_cacheFile;

  /**
   * @param string $file the cache-file.
   *
   * @param string $handler the dba handler.
   *
   * You have to install one of this handlers before use.
   *
   * cdb      = Tiny Constant Database - for reading.
   * cdb_make = Tiny Constant Database - for writing.
   * db4      = Oracle Berkeley DB 4   - for reading and writing.
   * qdbm     = Quick Database Manager - for reading and writing.
   * gdbm     = GNU Database Manager   - for reading and writing.
   * flatfile = default dba extension  - for reading and writing.
   *
   * Use flatfile-handler only when you cannot install one,
   * of the libraries required by the other handlers,
   * and when you cannot use bundled cdb handler.
   *
   * @param string $mode For read/write access, database creation if it doesn't currently exist.
   *
   * @param boolean $persistently
   *
   * @throws RuntimeException If no DBA extension or handler installed.
   */
  public function __construct($file, $handler = 'flatfile', $mode = 'c', $persistently = true)
  {
    if (false === extension_loaded('dba')) {
      throw new RuntimeException(
        'The DBA extension is required for this wrapper, but the extension is not loaded'
      );
    }

    if (false === in_array($handler, dba_handlers(false))) {
      throw new RuntimeException(
        'The ' . $handler . ' handler is required for the DBA extension, but the handler is not installed'
      );
    }

    $this->_dba = (true === $persistently)
      ? @dba_popen($file, $mode, $handler)
      : @dba_open($file, $mode, $handler);

    if ($this->_dba === false) {
      $err = error_get_last();
      throw new RuntimeException($err['message']);
    }

    $this->_cacheFile  = $file;
    $this->_handler    = $handler;
  }

  /**
   * Closes an open dba resource
   * @return void
   */
  public function __destruct()
  {
    $this->closeDba();
  }

  /**
   * @param string $identifier
   * @param mixed $object
   * @param int|bool $ltime Lifetime in seconds otherwise cache forever.
   * @return bool
   */
  public function put($identifier, $object, $ltime = false)
  {
    if (true === $this->has($identifier)) {
      return dba_replace($identifier, Serializer::serialize($object, $ltime), $this->_dba);
    }

    return dba_insert($identifier, Serializer::serialize($object, $ltime), $this->_dba);
  }

  /**
   * @param string $identifier
   * @param mixed $object
   * @return bool
   */
  public function forever($identifier, $object)
  {
    return $this->put($identifier, $object, false);
  }

  /**
   * @param string $identifier
   * @return bool
   */
  public function get($identifier)
  {
    $res = $this->fetch($identifier);

    if (false === $res) {
      return false;
    }

    if (false === $res->ltime || (microtime(true) - $res->mtime) < $res->ltime) {
      return $res->object;
    }

    $this->delete($identifier);

    return false;
  }

  /**
   * @return string
   */
  public function getCacheFile()
  {
    return $this->_cacheFile;
  }

  /**
   * @param string $identifier
   * @return Capsule|boolean
   */
  public function fetch($identifier)
  {
    $fetchObject = dba_fetch($identifier, $this->_dba);

    if (false === $fetchObject) {
      return false;
    }

    return Serializer::unserialize($fetchObject);
  }

  /**
   * @param string $identifier
   * @return boolean
   */
  public function delete($identifier)
  {
    if (false === is_resource($this->_dba)) {
      return false;
    }

    if ($this->erasable()) {
      return dba_delete($identifier, $this->_dba);
    }

    return true;
  }

  /**
   * @param string $identifier
   * @return boolean
   */
  public function has($identifier)
  {
    return dba_exists($identifier, $this->_dba);
  }

  /**
   * Close the handler.
   */
  public function closeDba()
  {
    if ($this->_dba) {
      @dba_close($this->_dba);
      $this->_dba = null;
    }
  }

  /**
   * @return resource
   */
  public function getDba()
  {
    return $this->_dba;
  }

  /**
   * @return ArrayObject of stored cache ids (string).
   */
  public function getIds()
  {
    $ids     = new ArrayObject();
    $dba     = $this->getDba();
    $pointer = dba_firstkey($dba);

    while ($pointer) {
      $ids->offsetSet(null, $pointer);
      $pointer = dba_nextkey($dba);
    }

    return $ids;
  }

  /**
   * Return an array of metadata for the given cache id.
   *
   * - expire = the expire timestamp
   * - mtime = timestamp of last modification time
   *
   * @param string $id cache id
   * @return array|boolean
   */
  public function getMetaData($id)
  {
    $res = $this->fetch($id);

    if ($res instanceof Capsule) {
      return array(
        'expire' => $res->mtime + $res->ltime,
        'mtime'  => $res->mtime,
      );
    }

    return false;
  }

  /**
   * Ensures if a single cache-item can be deleted.
   * @param null|string $handler
   * @return bool
   */
  public function erasable($handler = null)
  {
    $handler = (!$handler) ? $this->_handler : $handler;

    return in_array($handler, array('inifile', 'gdbm', 'qdbm', 'db4'), true);
  }
}
