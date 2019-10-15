<?php namespace jtk\simple_php_cache;

class BrainyCache extends ProxyCache {
  
  /** @var float */
  private $threshold;
  
  /**
   * @param Cache $cache
   * @param float $threshold in milliseconds
   */
  function __construct($cache, $threshold) {
    parent::__construct($cache);
    $this->threshold = $threshold;
  }
  
  /**
   * @param callable $func
   * @return callable
   */
  public function wrap($func) {
    return function() use ($func)
    {
      $arguments = func_get_args();
      $identifier = $this->identifierOf($arguments);

      $cached = $this->get($identifier);
      if($cached) {
        return $cached;
      }
      
      $startTime = microtime(true);
      $value = call_user_func_array($func, $arguments);
      $endTime = microtime(true);
      $deltaTime = $endTime - $startTime;
      if($deltaTime >= $this->threshold) {
        $this->set($identifier, $value);        
      }
      
      return $value;
    };
  }
  
  /**
   * Only makes sense to use if cached value will taken by reference.
   * 
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
    
    $startTime = microtime(true);
    $funcResult = $func($cachedValue);
    $endTime = microtime(true);
    $deltaTime = $endTime - $startTime;

    if(($deltaTime >= $this->threshold) 
       && (false === $alreadyCached)) {
      $this->set($identifier, $cachedValue);
    }
    
    return $funcResult;
  }
}
