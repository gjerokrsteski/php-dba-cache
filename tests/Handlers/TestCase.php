<?php

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'DummyFixtures.php';

class CacheHandlersTestCase extends PHPUnit_Framework_TestCase
{
  /**
   * @var stdClass
   */
  protected $_object;

  /**
   * @var string
   */
  protected $_identifier;

  /**
   * Prepares the environment before running a test.
   */
  protected function setUp()
  {
    parent::setUp();

    $stdClass        = new stdClass();
    $stdClass->title = 'Zweiundvierz';
    $stdClass->from  = 'Joe';
    $stdClass->to    = 'Jane';
    $stdClass->body  = new Dummy();

    $this->_object     = $stdClass;
    $this->_identifier = md5('stdClass' . time());
  }

  /**
   * Cleans up the environment after running a test.
   */
  protected function tearDown()
  {
    unset($this->_object, $this->_identifier);
    parent::tearDown();
  }
}
