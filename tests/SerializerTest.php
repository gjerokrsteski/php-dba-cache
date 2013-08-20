<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'DummyFixtures.php';

class SerializerTest extends PHPUnit_Framework_TestCase
{
  public function testCreatingNewObject()
  {
    $this->assertNotNull(new Serializer());
  }

  public function objectsProvider()
  {
    $stdClass        = new stdClass();
    $stdClass->title = 'Zweiundvierz';
    $stdClass->from  = 'Joe';
    $stdClass->to    = 'Jane';
    $stdClass->body  = new Dummy2();

    return array(
      array( new Dummy2() ),
      array( $stdClass ),
      array( new ZipArchive() ),
      array( new XMLReader() ),
      array( 'i am a string' ),
      array( 123456789 ),
      array(
        array(
          'boo'=> 1,
          'foo'=> 2,
          'laa'=> 3
        )
      )
    );
  }

  /**
   * @depends SerializerTest::testCreatingNewObject
   * @dataProvider objectsProvider
   */
  public function testSerializingSomeObjects($object)
  {
    Serializer::serialize($object);
  }

  /**
   * @depends SerializerTest::testCreatingNewObject
   * @depends SerializerTest::testSerializingSomeObjects
   * @dataProvider objectsProvider
   */
  public function testUnserializingSomeObjectsAndCompareThemEachOther($object)
  {
    $serialized = Serializer::serialize($object);

    $userItem = Serializer::unserialize($serialized);

    $this->assertEquals($object, $userItem->object);
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

    $path  = dirname(dirname(__FILE__)) . '/tests/_drafts/test-cache-with-simplexml.flatfile';

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

