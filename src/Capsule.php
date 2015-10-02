<?php

namespace PhpDbaCache;

/**
 * Cache Capsule Container
 *
 * @category PhpDbaCache
 */
class Capsule
{
    /**
     * Class name of the object
     * @var string
     */
    public $type;

    /**
     * The object that should be cached
     *
     * @var object
     */
    public $object;

    /**
     * If object or array should be vacuumed
     *
     * @var bool
     */
    public $fake;

    /**
     * Make-time as current unix timestamp in microseconds
     *
     * @var mixed
     */
    public $mtime;

    /**
     * Life-time as current unix timestamp in microseconds
     *
     * @var bool|int
     */
    public $ltime;

    /**
     * @param bool     $fake
     * @param int|bool $ltime
     * @param object   $object
     */
    public function __construct($fake, $ltime, $object)
    {
        $this->fake = $fake;
        $this->ltime = $ltime;
        $this->mtime = microtime(true);
        $this->object = $object;
        $this->type = get_class($object);
    }
}
