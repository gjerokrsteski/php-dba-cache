<?php
/**
* CacheDba
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
class CacheDba
{
    /**
     * @var resource
     */
    protected $_dba;

    /**
     * @var resource
     */
    protected $_handler;

    /**
     * @param string $file the cache-file.
     *
     * @param string $mode For read/write access,
     * database creation if it doesn't currently exist.
     *
     * @param string $handler the dba handler.
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
     * To enable support for the handlers
     * @see http://www.php.net/manual/en/dba.installation.php
     *
     * @param booelan $persistently
     */
    public function __construct($file, $mode = 'c', $handler = 'flatfile', $persistently = true)
    {
        if (false === extension_loaded('dba'))
        {
            throw new RuntimeException(
                'The DBA extension is required for this wrapper,'.
                ' but the extension is not loaded'
            );
        }

        if (false === in_array($handler, dba_handlers(false)))
        {
            throw new RuntimeException(
                'The '.$handler.' handler is required for the DBA extension,'.
                ' but the handler is not installed'
            );
        }

        if (false === is_file($file))
        {
            @fopen($file, 'w+');
        }

        $this->_dba = (true === $persistently)
                        ? dba_popen($file, $mode, $handler)
                        : dba_open($file, $mode, $handler);

        $this->_handler = $handler;
    }

    /**
     * @param string $identifier
     * @param object $object
     * @return boolean
     */
    public function put($identifier, $object)
    {
        $serializer = new CacheSerializer();

        if (true === $this->has($identifier))
        {
            return dba_replace($identifier, $serializer->serialize($object), $this->_dba);
        }

        return dba_insert($identifier, $serializer->serialize($object), $this->_dba);
    }

    /**
     * @param string $identifier
     * @param integer $expiration
     * @return object | false
     */
    public function get($identifier, $expiration = 300)
    {
        $fetchObject = dba_fetch($identifier, $this->_dba);

        if (false === $fetchObject)
        {
            return false;
        }

        $serializer = new CacheSerializer();
        $getObject  = $serializer->unserialize($fetchObject);

        if ((time() - $getObject->time) < $expiration)
        {
            return $getObject->object;
        }

        $this->delete($identifier);

        return false;
    }

    /**
     * @param string $identifier
     * @return boolean
     */
    public function delete($identifier)
    {
        if (false === is_resource($this->_dba))
        {
            return false;
        }

        return dba_delete($identifier, $this->_dba);
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
        dba_close($this->_dba);
    }

    /**
     * @return resource
     */
    public function getDba()
    {
        return $this->_dba;
    }

    /**
     * @return array of stored cache ids (string).
     */
    public function getIds()
    {
        $ids = array();
        $dba = $this->getDba();
        $pointer = dba_firstkey($dba);

        while($pointer)
        {
            $ids[] = $pointer;
            $pointer = dba_nextkey($dba);
        }

        return $ids;
    }
}