<?php
error_reporting(E_ALL | E_STRICT);

ini_set('display_errors', 1);

require_once dirname(dirname(__FILE__)).'/src/CacheDba.php';
require_once dirname(dirname(__FILE__)).'/src/CacheSerializer.php';
require_once dirname(dirname(__FILE__)).'/src/CacheGarbageCollector.php';

function _logger($message)
{
    error_log(
         '- TIME: '.time().' '.$message.PHP_EOL,
        3,
        dirname(dirname(__FILE__)).'/tests/_drafts/backend-penetration-test-info.log'
    );
}

function _loggIdentifiers($dba)
{
    $pointer = dba_firstkey($dba);

    while($pointer)
    {
        _logger('IDENTIFIER: '.$pointer);
        $pointer = dba_nextkey($dba);
    }
}

// prepare test-data.
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

$stdClass = new stdClass();
$stdClass->title = 'Hi firend, i am cached.';
$stdClass->from = 'Joe';
$stdClass->to = 'Hover';
$stdClass->body = 'Yes, it works!';

// create the cahe.
$path = dirname(dirname(__FILE__)).'/tests/_drafts/backend-penetration-test.flat';
$cache = new CacheDba($path);

_loggIdentifiers($cache->getDba());

// put some data to the cache.
$cache->put(md5('stdClass'), $stdClass);
_logger('PUT stdClass OBJECT & SLEEP 1 sec');
sleep(1);

$cache->put(md5('ZipArchive'), new ZipArchive());
_logger('PUT ZipArchive OBJECT & SLEEP 1 sec');
sleep(1);

$cache->put(md5('XMLReader'), new XMLReader());
_logger('PUT XMLReader OBJECT & SLEEP 1 sec');
sleep(1);

$cache->put(md5('SimpleXMLElement'), $simplexml);
_logger('PUT SimpleXMLElement OBJECT');

// generate random expiration-time.
srand(time());
$expiration = (rand()%20)+1;

// retrieve data from cache.
$getObjects = array();
$getObjects[] = array('stdClass', $cache->get(md5('stdClass'), $expiration));
$getObjects[] = array('ZipArchive', $cache->get(md5('ZipArchive'), $expiration));
$getObjects[] = array('XMLReader', $cache->get(md5('XMLReader'), $expiration));
$getObjects[] = array('SimpleXMLElement', $cache->get(md5('SimpleXMLElement'), $expiration));

// write information.
foreach ($getObjects as $getObject)
{
    _logger(
        'FETCH '.$getObject[0].' OBJECT BY EXPIRATION-TIME '.$expiration.' : '
        .print_r(($getObject[1] instanceof $getObject[0]) ? 'true' : 'false', true)
    );
}

$garbageCollection = new CacheGarbageCollector($cache);
$garbageCollection->cleanAll();

_logger(
    'CLEAN CACHE-GARBAGE-COLLECTION'
);

_loggIdentifiers($cache->getDba());

_logger('################################################');
