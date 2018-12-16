<?php namespace jtk\simple_php_cache;

abstract class Cache
{
  /**
   * @param string $identifier
   * @param mixed $default
   * @return mixed
   */
  public abstract function get($identifier, $default = null);
  
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
      $identifier = $this->identifierOf($arguments);

      return $this->get($identifier) ?:
        $this->set($identifier,
          call_user_func_array(
            $func,
            $arguments));
    };
  }
  
  /**
   * @param callback $func
   * @param string|mixed $identifier
   * @return mixed
   */
  public function with($func, $identifier) {
    $identifier = is_string($identifier) ? 
      $identifier : 
      $this->identifierOf($identifier);
    
    $cachedValue = $this->get($identifier, null);
    $alreadyCached = null !== $cachedValue;
    $funcResult = call_user_func($func, $cachedValue);
    
    if(false === $alreadyCached) {
      $this->set($identifier, $cachedValue);
    }
    
    return $funcResult;
  }
  
  /**
   * @param mixed $data
   * @return string
   */
  public function identifierOf($data) {
    return sha1(serialize($data));
  }
}
