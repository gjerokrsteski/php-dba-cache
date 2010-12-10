<?php
require_once dirname(dirname(__FILE__)).'/src/CacheSerializer.php';

class CacheSerializerTest
extends PHPUnit_Framework_TestCase
{
    public function testCreatingNewSerializerObject()
    {
        $this->assertNotNull(new CacheSerializer(new stdClass()));
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
            $serializer = new CacheSerializer($object);
            $serializer->serialize();
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
            $serializer = new CacheSerializer($object);

            $unserializer = new CacheSerializer($serializer->serialize());

            $this->assertEquals($object, $unserializer->unserialize()->object);
        }
        catch (Exception $e)
        {
            $this->fail(
                $e->getMessage().$e->getTraceAsString()
            );
        }
    }
}

