<?php namespace jtk\simple_php_cache;

class ArrayCache extends Cache
{
  /** @var array */
  private $array;


  /**
   * @param array $array
   */
  public function __construct(&$array)
  {
    $this->array = &$array;
  }

  /**
   * @param string $identifier
   * @param mixed $default
   * @return mixed
   */
  public function get($identifier, $default = null)
  {
    return $this->array[$identifier] ?: $default;
  }

  /**
   * @param string $identifier
   * @return boolean
   */
  public function has($identifier)
  {
    return isset($this->array[$identifier]);
  }

  /**
   * @param string $identifier
   * @param mixed $data
   * @return mixed
   */
  public function set($identifier, $data)
  {
    return $this->array[$identifier] = $data;
  }

  public function clear()
  {
    $this->array = [];
  }
}