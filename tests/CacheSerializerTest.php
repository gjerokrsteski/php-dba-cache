<?php
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
}

