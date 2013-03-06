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
 * @category  CacheDba
 * @copyright Copyright (c) 2010-2011 Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */
class Serializer
{
  /**
   * @param $object
   * @param bool $ltime
   * @return string
   * @throws RuntimeException
   */
  public static function serialize($object, $ltime = false)
  {
    $masked = false;

    if (false === is_object($object)) {
      $object = (object)$object;
      $masked = true;
    }

    $capsule         = new Capsule();
    $capsule->type   = get_class($object);
    $capsule->object = $object;
    $capsule->fake   = $masked;
    $capsule->mtime  = microtime(true);
    $capsule->ltime  = $ltime;

    if ($object instanceof SimpleXMLElement) {
      $capsule->object = $object->asXml();
    }

    $res = @serialize($capsule);

    if ($res === false) {
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
  public static function unserialize($object)
  {
    $capsule = @unserialize($object);

    if ($capsule === false) {
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