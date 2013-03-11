Introduction
============

The php-dba-cache uses the database (dbm-style) abstraction layer to cache/store your PHP objects, 
strings, integers or arrays. Even instances of SimpleXMLElement can be put to the cache. You dont 
have to matter about the size of the cache-file. It depends on the free space of your disk.

Available options
===========================

Cache
- Open a given dba database
- Insert a new record with a given key (persistently or with a given expiration time)
- Get a record with a given key
- Replace the value of a record with a given key
- Delete the record with a given key
- Return metadata for the given key: expire timestamp & timestamp of last modification time
- Get all keys from cache
    
Sweeper - CacheGarbageCollector (optional)
- Clean all entries
- Clean expired entries
- Flush the cache file
- Optimizes the database file automatically after cleaning process

Installation
============

"By using the --enable-dba=shared configuration option you can build a dynamic loadable module
to enable PHP for basic support of dbm-style databases. You also have to add support for at
least one of the following handlers by specifying the --with-XXXX configure switch
to your PHP configure line."
    
More about installation: http://www.php.net/manual/en/dba.installation.php

DBA handlers
============

The behaviour of various aspects for the caching depends on the implementation of yor
installed dba-type database. I have tested it with several database-handlers like db4, flatfile,
cdb, cdb_make, gdbm. The cdb & cdb_make family is the fastest, but you have to create
you own garbage-collection-cleaner or you can delete it manually. Take a look at the tests
(https://github.com/gjerokrsteski/php-dba-cache/tree/master/tests) to better understand
how to use the cache.

cdb = Tiny Constant Database - for reading
Cdb is "a fast, reliable, lightweight package for creating and reading constant databases.
" It is from the author of qmail and can be found at http://cr.yp.to/cdb.html. Since it is
constant, we support only reading operations. And since PHP 4.3.0 we support writing
(not updating) through the internal cdb library.

cdb_make = Tiny Constant Database - for writing
Since PHP 4.3.0 we support creation of cdb files when the bundled cdb library is used.

db4 = Oracle Berkeley DB 4 - for reading and writing
DB4 is Sleepycat Software's DB4. This is available since PHP 4.3.2.

gdbm = GNU Database Manager - for reading and writing
Gdbm is the GNU database manager.

flatfile = default dba extension - for reading and writing
This is available since PHP 4.3.0 for compatibility with the deprecated dbm extension only
and should be avoided. However you may use this where files were created in this format.
That happens when configure could not find any external library.
    
More about requirements: http://www.php.net/manual/en/dba.requirements.php


Nice to know
------------

Not all of the DBA-style databases can replace key-value pairs, like the CDB. The CDB database
can handle only with fixed key-value pairs. The best and fastest handlers for DBA-style caching
are: QDBM, Berkeley DB (DB4), NDBM and least the Flatfile.
Most problematic are dbm and ndbm which conflict with many installations. The reason for this is
that on several systems these libraries are part of more than one other library. The configuration
test only prevents you from configuring malfaunctioning single handlers but not combinations.

Sample for Oracle Berkeley DB 4 with persistent connection
----------------------------------------------------------

```php
$cache = new Cache(
  '/your/path/to/the/cahe-file/cache.db4', 'db4'
);

$yorObject            = new YourObjectYouWantToPutInCache();
$yourObjectIdentifier = 'md5(get_class($yorObject))';

// Check if your object is in the cache.
// You also can ignore it, and let the CacheDba do it for you.
if (false == $cache->has($yourObjectIdentifier)) {
  $cache->delete($yourObjectIdentifier);
}

$cache->put($yourObjectIdentifier, $yorObject);

// Than somewhere at your project.
$cache->get($yourObjectIdentifier);

// For the garbage collection 
// you can create an cron-job starting once a day.
$sweeper = new Sweeper($cache);
$sweeper->cleanAll();

// or clean all objects older than given expiration since now.
$sweeper->cleanOld();
```

Saving SimpleXMLElement instances into DB 4 with persistent connection
----------------------------------------------------------------------

```php
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

$path = dirname(__FILE__).'/simple-xml-test-cache.db4';
$cache = new Cache($path, 'db4');

$cache->put($identifier, $simplexml);

$getObject = $cache->get($identifier);

error_log(' - PUT IN CACHE : '.print_r($simplexml, true));
error_log(' - GET FROM CACHE : '.print_r($getObject, true));

error_log(' - IS SAME OBJECT : '.
    print_r(($simplexml->asXml() === $getObject->asXml())
            ? 'true' : 'false', true));
```


Benchmark Test of DBM Brothers
------------------------------

This benchmark test is to calculate processing time (real time)
and file size of database. Writing test is to store 1,000,000 records. Reading test is
to fetch all of its records. Both of the key and the value of each record are such 8-byte
strings as `00000001', `00000002', `00000003'... Tuning parameters of each DBM are set to
display its best performance. Platform: Linux 2.4.31 kernel, EXT2 file system,
Pentium4 1.7GHz CPU, 1024MB RAM, ThinkPad T42 Compilation: gcc 3.3.2 (using -O3), glibc 2.3.3

Result
```cli
NAME        DESCRIPTION                             WRITE TIME  READ TIME   FILE SIZE
QDBM        Quick Database Manager 1.8.74           1.89        1.58        55257
NDBM        New Database Manager 5.1                8.07        7.79        814457
SDBM        Substitute Database Manager 1.0.2       11.32       0.00        606720
GDBM        GNU Database Manager 1.8.3              14.01       5.36        82788
TDB         Trivial Database 1.0.6                  9.64        2.22        51056
CDB         Tiny Constant Database 0.75             0.87        0.80        39065
BDB         Berkeley DB 4.4.20                      9.62        5.62        40956
QDBM-BT-ASC B+ tree API of QDBM (ascending order)   2.37        1.78        24304
QDBM-BT-RND B+ tree API of QDBM (at random)         10.90       4.82        15362
BDB-BT-ASC B+ tree API of BDB (ascending order)     3.04        3.06        27520
BDB-BT-RND B+ tree API of BDB (at random)           10.03       4.15        29120
```

Unit of time is seconds. Unit of size is kilo bytes. Read time of SDBM can not be calculated
because its database is broken when more than 100000 records.
