<?php

require_once(dirname(__FILE__) . '/Cache.php');

class MemoryCache
extends Cache
{
  private $memory;


  public function __construct(&$memory)
  {
    $this->memory = &$memory;
  }

  public function get($identifier)
  {
    return $this->memory[$identifier];
  }

  public function has($identifier)
  {
    return isset($this->memory[$identifier]);
  }

  public function set($identifier, $data)
  {
    return $this->memory[$identifier] = $data;
  }

  public function clear()
  {
    $this->memory = [];
  }
}