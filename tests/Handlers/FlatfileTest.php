<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'TestCase.php';

class FlatfileTest extends CacheHandlersTestCase
{
  protected function setUp()
  {
    parent::setUp();

    $this->_general_file    = dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache.flatfile';
    $this->_general_handler = 'flatfile';
    $this->_general_mode    = 'c-';
  }

  public function testWriteAndReadWithoutPersistentConnection()
  {
    try {
      $cache = new Cache(
        dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache-pers.flatfile', 'flatfile', 'c-', false
      );
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    $this->assertInstanceOf('Cache', $cache);

    $cache->put($this->_identifier, array('rambo' => 123));

    $res = $cache->get($this->_identifier);

    $this->assertInternalType('array', $res);
    $this->assertEquals($res, array('rambo' => 123));
  }

  public function testHandlingWithSimpleXMLElementIntoFlatfileHandler()
  {
    $identifier = md5(uniqid());

    // make a xml-file of 1000 nodes.
    $string = "<?xml version='1.0'?>
        <document>";
    for ($i = 1; $i <= 100; $i++) {
      $string .= "<item>
			 <title>Let us cache</title>
			 <from>Joe</from>
			 <to>Jane</to>
			 <body>Some content here</body>
                 </item>";
    }
    $string .= "</document>";

    $simplexml = simplexml_load_string(
      $string, 'SimpleXMLElement', LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NONET
    );

    $path  = dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/test-cache-with-simplexml.flatfile';

    try {
      $cache = new Cache($path);
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    $cache->put($identifier, $simplexml);
    $object_from_cache = $cache->get($identifier);
    $cache->closeDba();

    $this->assertEquals($simplexml->asXML(), $object_from_cache->asXML());

    @unlink($path);
  }
}
