<?php namespace jtk\simplePhpCache;

class FileCache extends Cache
{
  /** @var string */
  private $path;

  /** @var int */
  private $size;
  
  /** @var Cache */
  private $memoryCache;


  /**
   * @param string $path
   * @param int $size
   */
  public function __construct($path, $size)
  {
    $this->path = $path;
    $this->size = $size;
    
    $memory = [];
    $this->memoryCache = new ArrayCache($memory);
  }

  public function __destruct()
  {
    unset($this->memoryCache);
    $this->clearSize();
  }

  /**
   * @param string $identifier
   * @return mixed
   */
  public function get($identifier)
  {
    return $this->memoryCache->has($identifier) ?
      $this->memoryCache->get($identifier) :
      $this->memoryCache->set($identifier, 
        unserialize(
          file_get_contents(
            $this->determineFilepath($identifier))));
  }

  /**
   * @param string $identifier
   * @return boolean
   */
  public function has($identifier)
  {
    return $this->memoryCache->has($identifier) || 
           file_exists($this->determineFilepath($identifier));
  }

  /**
   * @param string $identifier
   * @param mixed $data
   * @return mixed
   */
  public function set($identifier, $data)
  {
    file_put_contents(
      $this->determineFilepath($identifier),
      serialize($data),
      LOCK_EX);
    
    $this->memoryCache->set($identifier, $data);

    return $data;
  }

  public function clear()
  {
    $this->memoryCache->clear();

    $directory = dir($this->path);
    if($directory != false)
    {
      while(($entry = $directory->read()) != false)
      {
        if(is_file($this->path . $entry))
        {
          unlink($this->path . $entry);
        }
      }

      $directory->close();
    }
  }

  private function determineFilepath($identifier)
  {
    return $this->path . sha1($identifier);
  }
  
  private function clearSize()
  {
    $files = $this->determineAgeSortedFiles();
    for($i = count($files)-1; $i >= $this->size; --$i)
    {
      unlink($this->path . $files[$i]);
    }
  }

  // TODO: Create one meta file to manage this not reading directory index
  private function determineAgeSortedFiles()
  {
    $files = [];

    $directory = dir($this->path);
    if($directory != false)
    {
      while(($entry = $directory->read()) != false)
      {
        if(is_file($this->path . $entry))
        {
          $files[$entry] = filemtime($this->path . $entry);
        }
      }
      $directory->close();
    }

    asort($files);
    return array_keys($files);
  }
}