<?php namespace jtk\simple_php_cache;

class FileCache extends Cache
{
  /** @var string */
  private $directory;

  /** @var int */
  private $itemLimit;
  
  /** @var Cache */
  private $inMemory;


  /**
   * @param string $directory
   * @param int $itemLimit
   */
  public function __construct($directory, $itemLimit)
  {
    $this->directory = $directory;
    $this->itemLimit = $itemLimit;
    
    $memory = [];
    $this->inMemory = new ArrayCache($memory);
  }

  public function __destruct()
  {
    unset($this->inMemory);
    $this->limitItemCount();
  }

  /**
   * @param string $identifier
   * @param mixed $default
   * @return mixed
   */
  public function get($identifier, $default = null)
  {
    return $this->inMemory->get($identifier, null) ?:
      $this->inMemory->set($identifier, 
        (unserialize(
          file_get_contents(
            $this->determineItemPath($identifier))) ?:
        $default));
  }

  /**
   * @param string $identifier
   * @return boolean
   */
  public function has($identifier)
  {
    return $this->inMemory->has($identifier) || 
           file_exists($this->determineItemPath($identifier));
  }

  /**
   * @param string $identifier
   * @param mixed $data
   * @return mixed
   */
  public function set($identifier, $data)
  {
    file_put_contents(
      $this->determineItemPath($identifier),
      serialize($data),
      LOCK_EX);
    
    $this->inMemory->set($identifier, $data);

    return $data;
  }

  public function clear()
  {
    $this->inMemory->clear();

    $directory = dir($this->directory);
    if($directory != false)
    {
      while(($entry = $directory->read()) != false)
      {
        if(is_file($this->directory . $entry))
        {
          unlink($this->directory . $entry);
        }
      }

      $directory->close();
    }
  }

  private function determineItemPath($identifier)
  {
    return $this->directory . sha1($identifier);
  }
  
  private function limitItemCount()
  {
    $files = $this->determineAgeSortedFiles();
    for($i = count($files)-1; $i >= $this->itemLimit; --$i)
    {
      unlink($this->directory . $files[$i]);
    }
  }

  private function determineAgeSortedFiles()
  {
    $files = [];

    $directory = dir($this->directory);
    if($directory != false)
    {
      while(($entry = $directory->read()) != false)
      {
        if(is_file($this->directory . $entry))
        {
          $files[$entry] = filemtime($this->directory . $entry);
        }
      }
      $directory->close();
    }

    asort($files);
    return array_keys($files);
  }
}