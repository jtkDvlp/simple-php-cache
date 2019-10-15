<?php namespace jtk\simple_php_cache;

abstract class ProxyCache extends Cache {
  /** @var Cache */
  private $cache;
  
  
  /**
   * @param Cache $cache
   */
  public function __construct($cache) {
    $this->cache = $cache;
  }
  
  /**
   * @param string $identifier
   * @param mixed $default
   * @return mixed
   */
  public function get($identifier, $default = null)
  {
    return $this->cache->get($identifier, $default);
  }

  /**
   * @param string $identifier
   * @return boolean
   */
  public function has($identifier)
  {
    return $this->cache->has($identifier);
  }

  /**
   * @param string $identifier
   * @param mixed $data
   * @return mixed
   */
  public function set($identifier, $data)
  {
    return $this->cache->set($identifier, $data);
  }

  public function clear()
  {
    $this->cache->clear();
  }
}
