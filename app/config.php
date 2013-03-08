<?php
$config = array(

  /*
   * The path to the cache file.
   */
  'file'         => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app.cache',

  /*
   * You have to install one of this handlers before use.
   *
   * cdb      = Tiny Constant Database - for reading.
   * cdb_make = Tiny Constant Database - for writing.
   * db4      = Oracle Berkeley DB 4   - for reading and writing.
   * qdbm     = Quick Database Manager - for reading and writing.
   * gdbm     = GNU Database Manager   - for reading and writing.
   * inifile  = Ini File               - for reading and writing.
   * flatfile = default dba extension  - for reading and writing.
   *
   * Use flatfile-handler only when you cannot install one,
   * of the libraries required by the other handlers,
   * and when you cannot use bundled cdb handler.
   */
  'handler'      => 'db4',

  /*
   * The mode for read/write access, database creation if it doesn't currently exist.
   *
   * r  = for read access
   * w  = for read/write access to an already existing database
   * c  = for read/write access and database creation if it doesn't currently exist
   * n  = for create, truncate and read/write access
   */
  'mode'         => 'c',

  /*
   * Open the database persistently
   */
  'persistently' => false,

  /*
   * The date for mat for the view.
   */
  'date_format' => 'Y-m-d H:i:s',

  /*
   * Please define the authentication data for using the super-user options.
   */
  'authentication' => array(
    'username'  => 'admin',
    'password'  => 'dba',
  ),

);