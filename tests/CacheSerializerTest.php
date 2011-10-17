<?php
require_once dirname(dirname(__FILE__)).'/src/CacheDba.php';
require_once dirname(dirname(__FILE__)).'/src/CacheSerializer.php';

class CacheSerializerTest
extends PHPUnit_Framework_TestCase
{
    public function testCreatingNewSerializerObject()
    {
        $this->assertNotNull(new CacheSerializer());
    }

    public function objectsProvider()
    {
        $stdClass = new stdClass();
        $stdClass->title = 'Zweiundvierz';
        $stdClass->from = 'Joe';
        $stdClass->to = 'Jane';
        $stdClass->body = 'Ich kenne die Antwort -- aber was ist die Frage?';

        return array(
          array($stdClass),
          array(new ZipArchive()),
          array(new XMLReader()),
          array('i am a string'),
          array(123456789),
          array(array(1, 2, 3, '4' => 5, '6'=>7))
        );
    }

    /**
     * @depends CacheSerializerTest::testCreatingNewSerializerObject
     * @dataProvider objectsProvider
     */
    public function testSerializingSomeObjects($object)
    {
        try
        {
            $serializer = new CacheSerializer();
            $serializer->serialize($object);
        }
        catch (Exception $e)
        {
            $this->fail(
                $e->getMessage().$e->getTraceAsString()
            );
        }
    }

    /**
     * @depends CacheSerializerTest::testCreatingNewSerializerObject
     * @depends CacheSerializerTest::testSerializingSomeObjects
     * @dataProvider objectsProvider
     */
    public function testUnserializingSomeObjectsAndCompareEachother($object)
    {
        try
        {
            $unserializer = new CacheSerializer();

            $serialized = $unserializer->serialize($object);

            $this->assertEquals($object, $unserializer->unserialize($serialized)->object);
        }
        catch (Exception $e)
        {
            $this->fail(
                $e->getMessage().$e->getTraceAsString()
            );
        }
    }

    public function testHandlingWithSimpleXMLElement()
    {
        $identifier = md5(uniqid());

        $string = "<?xml version='1.0'?>
        <document>
         <title>Let us cache</title>
         <from>Joe</from>
         <to>Jane</to>
         <body>Some content here</body>
        </document>";

        $simplexml = simplexml_load_string(
            $string,
            'SimpleXMLElement',
            LIBXML_NOERROR|LIBXML_NOWARNING|LIBXML_NONET
        );

        $path = dirname(dirname(__FILE__)).'/tests/_drafts/test-cache-with-simplexml.db4';
        $cache = new CacheDba($path, new CacheSerializer(), 'c', 'db4', true);
        $cache->put($identifier, $simplexml);
        $object_from_cache = $cache->get($identifier);
        $cache->closeDba();

        $this->assertEquals($simplexml->asXML(), $object_from_cache->asXML());
    }
}

