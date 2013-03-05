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
   * @param mixed $object
   * @param bool $ltime
   * @return string containing a byte-stream representation.
   */
  public static function serialize($object, $ltime = false)
  {
    $masked = false;

    if (false === is_object($object)) {
      $object = static::mask($object);
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

    return serialize($capsule);
  }

  /**
   * @param string $object
   * @return Capsule
   */
  public static function unserialize($object)
  {
    $capsule = unserialize($object);

    if (true === $capsule->fake) {
      $capsule->object = static::unmask($capsule->object);
    }

    if ($capsule->type == 'SimpleXMLElement') {
      $capsule->object = simplexml_load_string($capsule->object);
    }

    return $capsule;
  }

  /**
   * @param $item
   * @return object
   */
  private static function mask($item)
  {
    return (object)$item;
  }

  /**
   * @param $item
   * @return array
   */
  private static function unmask($item)
  {
    if (isset($item->scalar)) {
      return $item->scalar;
    }

    return (array)$item;
  }
}