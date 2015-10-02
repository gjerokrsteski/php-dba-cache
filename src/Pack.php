<?php

namespace PhpDbaCache;

/**
 * Cache Serializer
 *
 * @category PhpDbaCache
 */
class Pack
{
    /**
     * @param          $object
     * @param int|bool $ltime
     *
     * @return string
     * @throws \RuntimeException If problems by serializing
     */
    public static function wrap($object, $ltime = false)
    {
        $fake = false;

        if (false === is_object($object)) {
            $object = (object)$object;
            $fake = true;
        }

        $capsule = new Capsule($fake, $ltime, $object);

        if ($object instanceof \SimpleXMLElement) {
            $capsule->object = $object->asXml();
        }

        if (false === ($res = @serialize($capsule))) {
            $err = error_get_last();
            throw new \RuntimeException($err['message']);
        }

        return $res;
    }

    /**
     * @param $object
     *
     * @return Capsule
     * @throws \RuntimeException If problems on un-serializing
     */
    public static function unwrap($object)
    {
        $serialized = (false !== ($capsule = @unserialize(trim($object))));

        if (!$serialized) {
            $err = error_get_last();
            throw new \RuntimeException($err['message']);
        }

        if (true === $capsule->fake) {
            if (isset($capsule->object->scalar)) {
                $capsule->object = $capsule->object->scalar;
            } else {
                $capsule->object = (array)$capsule->object;
            }
        }

        if ($capsule->type == 'SimpleXMLElement') {
            $capsule->object = simplexml_load_string($capsule->object);
        }

        return $capsule;
    }
}
