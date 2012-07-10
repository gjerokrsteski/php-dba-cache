<?php

class Dummy
{
  private $foo1 = 123;
  protected $bar2 = array(1,2,3);
  protected $moo3 = 'moo';
}


class Dummy1
{
  private $foo = 123;
  protected $bar = array(1,2,3);
  public $moo = 'moo';
}

class Dummy2 extends Dummy1
{
  public function __construct()
  {
    $this->moo = new Dummy1();
  }
}