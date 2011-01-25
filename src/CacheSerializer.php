<?php
/**
* CacheSerializer
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
* @category CacheDba
* @copyright Copyright (c) 2010-2011 Gjero Krsteski (http://krsteski.de)
* @license http://krsteski.de/new-bsd-license New BSD License
*/

/**
 * CacheSerializer
 *
 * @category   CacheDba
 * @copyright Copyright (c) 2010-2011 Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */
class CacheSerializer
{
    /**
     * @var object
     */
    public $object;

    /**
     * @param object $object
     */
    public function __construct($object)
    {
        $this->object = $object;
    }

    /**
     * Serialize the object as stdClass.
     * @return string containing a byte-stream representation.
     */
    public function serialize()
    {
        $objectInformation = new stdClass();

        $objectInformation->type   = get_class($this->object);
        $objectInformation->object = $this->object;
        $objectInformation->time   = time();

        if ($this->object instanceof SimpleXMLElement)
        {
            $objectInformation->object = $this->object->asXml();
        }

        return serialize($objectInformation);
    }

    /**
     * Unserialize the object.
     * @return stdClass
     */
    public function unserialize()
    {
        $objectInformation = unserialize($this->object);

        if ($objectInformation->type == 'SimpleXMLElement')
        {
            $objectInformation->object = simplexml_load_string($objectInformation->object);
        }

        return $objectInformation;
    }
}