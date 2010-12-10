<?php
/**
 * PhpUnit Tests
 *
 * The global configuration for the tests.
 */

error_reporting(E_ALL | E_STRICT);

ini_set('display_errors', 1);

date_default_timezone_set('Europe/Berlin');

set_include_path(
    dirname(dirname(__FILE__))
    .'/php-dba-cache'
    .PATH_SEPARATOR
    .dirname(__FILE__)
    .PATH_SEPARATOR
    .get_include_path()
);
