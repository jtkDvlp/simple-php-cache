<?php namespace jtk\simple_php_cache;

abstract class Cache
{
  /**
   * @param string $identifier
   * @return string
   */
  public abstract function get($identifier);
  
  /**
   * @param string $identifier
   * @return boolean
   */
  public abstract function has($identifier);
  
  /**
   * @param string $identifier
   * @param mixed $data
   * @return mixed
   */
  public abstract function set($identifier, $data);

  public abstract function clear();

  /**
   * @param callable $func
   * @return callable
   */
  public function wrap($func)
  {
    return function() use ($func)
    {
      $arguments = func_get_args();
      $identifier = sha1(serialize($arguments));

      return $this->has($identifier) ?
        $this->get($identifier) :
        $this->set($identifier,
          call_user_func_array(
            $func,
            $arguments));
    };
  }
}
