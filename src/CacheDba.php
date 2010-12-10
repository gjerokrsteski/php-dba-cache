<?php
/**
 * CacheDba
 *
 * @category   CacheDba
 * @author     Gjero Krsteski <gjero@krsteski.de>
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
     * @param string $path to cache-file.
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
    public function __construct($path, $mode = 'c', $handler = 'flatfile', $persistently = true)
    {
        $this->_dba = (true === $persistently)
                        ? dba_popen($path, $mode, $handler)
                        : dba_open($path, $mode, $handler);

        $this->_handler = $handler;
    }

    /**
     * @param string $identifier
     * @param object $object
     * @return boolean
     */
    public function put($identifier, $object)
    {
        $serializer = new CacheSerializer($object);

        if (true === $this->has($identifier))
        {
            return dba_replace($identifier, $serializer->serialize(), $this->_dba);
        }

        return dba_insert($identifier, $serializer->serialize(), $this->_dba);
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

        $serializer = new CacheSerializer($fetchObject);
        $getObject  = $serializer->unserialize();

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
}