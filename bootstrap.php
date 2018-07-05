<?php
ini_set('default_charset', 'UTF-8');
date_default_timezone_set('UTC');

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR, true);
}
if (!defined('BASE_PATH')) {
    define('BASE_PATH', realpath(dirname(__FILE__)) . DS, true);
}

require_once BASE_PATH . 'app' . DS . 'config.php';

spl_autoload_register(
    function ($class) {
        static $classes = array(
            'PhpDbaCache\\Cache'   => '/Cache.php',
            'PhpDbaCache\\Pack'    => '/Pack.php',
            'PhpDbaCache\\Sweep'   => '/Sweep.php',
            'PhpDbaCache\\Capsule' => '/Capsule.php',
        );

        if (isset($classes[$class])) {
            require BASE_PATH . '/src' . $classes[$class];
        }

        return false;
    }
);
