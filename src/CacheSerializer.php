<?php
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