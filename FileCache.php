<?php

require_once(dirname(__FILE__) . '/Cache.php');

class FileCache
extends Cache
{
  private $path;
  private $size;


  public function __construct($path, $size)
  {
    $this->path = $path;
    $this->size = $size;
  }

  public function __destruct()
  {
    $this->clearSize();
  }

  public function get($identifier)
  {
    return unserialize(
      file_get_contents(
        $this->determineFilepath($identifier)));
  }

  public function has($identifier)
  {
    return file_exists($this->determineFilepath($identifier));
  }

  public function set($identifier, $data)
  {
    file_put_contents(
      $this->determineFilepath($identifier),
      serialize($data),
      LOCK_EX);

    return $data;
  }

  public function clear()
  {
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
    $files = $this->collectFiles();
    $count = count($files);

    ksort($files);
    $sortedFiles = array_values($files);

    for($i = 0;
        ($count - $i) > $this->size;
        ++$i)
    {
      unlink($this->path . $sortedFiles[$i]);
    }
  }

  private function collectFiles()
  {
    $directory = dir($this->path);
    $files = [];


    if($directory != false)
    {
      while(($entry = $directory->read()) != false)
      {
        if(is_file($this->path . $entry))
        {
          $files[filemtime($this->path . $entry)] = $entry;
        }
      }

      $directory->close();
    }

    return $files;
  }
}