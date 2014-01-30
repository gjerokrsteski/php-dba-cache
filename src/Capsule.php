<?php
/**
 * CacheCapsule
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
 * Cache Capsule Container
 *
 * @category  CacheDba
 */
class Capsule
{
  public $type, $object, $fake, $mtime, $ltime;

  /**
   * @param bool $fake
   * @param int|bool $ltime
   * @param object $object
   */
  public function __construct($fake, $ltime, $object)
  {
    $this->fake   = $fake;
    $this->ltime  = $ltime;
    $this->mtime  = microtime(true);
    $this->object = $object;
    $this->type   = get_class($object);
  }
}
