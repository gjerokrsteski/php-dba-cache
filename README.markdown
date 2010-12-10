Introduction
============

    The php-dba-cache uses the Database (dbm-style) Abstraction Layer to cache your objects.

Requirements & Installation
===========================

    The behaviour of various aspects for the caching depends on the implementation of the
    underlying database. I have tested it with several database-handlers like db4, flatfile,
    cdb, cdb_make, gdbm. The cdb & cdb_make family is the fastest, but you have to create
    you own garbage-collection-cleaner or you can delete it manually. Take a look at the tests
    (https://github.com/gjerokrsteski/php-dba-cache/tree/master/tests) to better understand
    how to use the cache.

    cdb = Tiny Constant Database - for reading.
    Cdb is "a fast, reliable, lightweight package for creating and reading constant databases.
    " It is from the author of qmail and can be found at http://cr.yp.to/cdb.html. Since it is
    constant, we support only reading operations. And since PHP 4.3.0 we support writing
    (not updating) through the internal cdb library.

    cdb_make = Tiny Constant Database - for writing.
    Since PHP 4.3.0 we support creation of cdb files when the bundled cdb library is used.

    db4 = Oracle Berkeley DB 4 - for reading and writing.
    DB4 is Sleepycat Software's DB4. This is available since PHP 4.3.2.

    gdbm = GNU Database Manager - for reading and writing.
    Gdbm is the GNU database manager.

    flatfile = default dba extension - for reading and writing.
    This is available since PHP 4.3.0 for compatibility with the deprecated dbm extension only
    and should be avoided. However you may use this where files were created in this format.
    That happens when configure could not find any external library.

    By using the --enable-dba=shared configuration option you can build a dynamic loadable module
    to enable PHP for basic support of dbm-style databases. You also have to add support for at
    least one of the following handlers by specifying the --with-XXXX configure switch
    to your PHP configure line.

Nice to know
------------

    After configuring and compiling PHP you must execute the following test from commandline:
    php run-tests.php ext/dba.     This shows whether your combination of handlers works. Most
    problematic are dbm and ndbm which conflict with many installations. The reason for this is
    that on several systems these libraries are part of more than one other library. The configuration
    test only prevents you from configuring malfaunctioning single handlers but not combinations.

Sample for Oracle Berkeley DB 4 with persistent connection
----------------------------------------------------------

    <?php
    $path = '/your/path/to/the/cahe-file/cache.db4';
    $cache = new CacheDba($path, 'c', 'db4', true);

    $yorObject = new YourObjectYouWantToPutInCache();
    $yourObjectIdentifier = md5(get_class($yorObject));

    // Check if your object is in the cache.
    // You also can ignore it, and let the CacheDba do it for you.
    if (false == $cache->has($yourObjectIdentifier))
    {
        $cache->delete($yourObjectIdentifier);
    }

    $cache->put($yourObjectIdentifier, $yorObject);

    // Than somewhere at your project.
    $cache->get($yourObjectIdentifier);


    // For the garbage collection you can create an cron-job stating once a day.
    $garbageCollection = new CacheGarbageCollector($cache);
    $garbageCollection->cleanAll();

    // or clean all objects older
    // than expiration of 5 minutes == 300 seconds since now.

    $garbageCollection->cleanByExpiration(300);
    ?>

Benchmark Test of DBM Brothers
------------------------------

    This benchmark test is to calculate processing time (real time)
    and file size of database. Writing test is to store 1,000,000 records. Reading test is
    to fetch all of its records. Both of the key and the value of each record are such 8-byte
    strings as `00000001', `00000002', `00000003'... Tuning parameters of each DBM are set to
    display its best performance. Platform: Linux 2.4.31 kernel, EXT2 file system,
    Pentium4 1.7GHz CPU, 1024MB RAM, ThinkPad T42 Compilation: gcc 3.3.2 (using -O3), glibc 2.3.3

    Result

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

    Unit of time is seconds. Unit of size is kilo bytes. Read time of SDBM can not be calculated
    because its database is broken when more than 100000 records.