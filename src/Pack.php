<?php
/**
 * Cache Serializer
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
 */

/**
 * Cache Serializer
 *
 * @category  CacheDba
 */
class Pack
{
  /**
   * @param $object
   * @param bool $ltime
   * @return string
   * @throws RuntimeException
   */
  public static function in($object, $ltime = false)
  {
    $fake = false;

    if (false === is_object($object)) {
      $object = (object)$object;
      $fake   = true;
    }

    $capsule = new Capsule($fake, $ltime, $object);

    if ($object instanceof SimpleXMLElement) {
      $capsule->object = $object->asXml();
    }

    if (false === ($res = @serialize($capsule))) {
      $err = error_get_last();
      throw new RuntimeException($err['message']);
    }

    return $res;
  }

  /**
   * @param $object
   * @return Capsule
   * @throws RuntimeException
   */
  public static function out($object)
  {
    $serialized = (false !== ($capsule = @unserialize(trim($object))));

    if (!$serialized) {
      $err = error_get_last();
      throw new RuntimeException($err['message']);
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