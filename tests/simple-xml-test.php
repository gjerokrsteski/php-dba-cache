<?php
error_reporting(E_ALL | E_STRICT);

ini_set('display_errors', 1);

require_once dirname(dirname(__FILE__)).'/src/CacheDba.php';
require_once dirname(dirname(__FILE__)).'/src/CacheSerializer.php';

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

$identifier = md5('simplexml_identifier');

$path = dirname(dirname(__FILE__)).'/tests/_drafts/simple-xml-test-cache.db4';
$cache = new CacheDba($path, 'c', 'db4', true);

$cache->put($identifier, $simplexml);

$getObject = $cache->get($identifier);

error_log(' - PUT IN CACHE : '.print_r($simplexml, true));
error_log(' - GET FROM CACHE : '.print_r($getObject, true));
error_log(' - IS SAME OBJECT : '.print_r(($simplexml->asXml() === $getObject->asXml()) ? 'true' : 'false', true));

?>
