<?php 
use jtk\simple_php_cache\FileCache;
use jtk\simple_php_cache\ArrayCache;

require __DIR__ . '/vendor/autoload.php';

session_start();

function example($cache)
{
  var_dump(strval(get_class($cache)));



  $identifier = "my_example_has_get_set";
  if($cache->has($identifier))
  {
    var_dump($cache->get($identifier));
  }
  else
  {
    var_dump($cache->set($identifier, "my value"));
  }



  $function = $cache->wrap(function($x, $y)
  {
    return $x + $y;
  });

  for($i = 0; $i < 10000; ++$i)
  {
    var_dump($function(rand(0, 9), rand(0, 9)));
  }
}

$start = microtime(true);
if(!file_exists('./cache/'))
{
  mkdir('./cache/');
}

$filecache = new FileCache(__DIR__.'/cache/', 50);
//$cache->clear();
example($filecache);

echo 'time:' . (microtime(true) - $start);



$start = microtime(true);
if(!isset($_SESSION['cache']))
{
  $_SESSION['cache'] = [];
}

$sessioncache = new ArrayCache(
  $_SESSION['cache']);
//$cache->clear();
example($sessioncache);

echo 'time:' . (microtime(true) - $start);



$filecache->set('session', $_SESSION);
var_dump($filecache->get('session'));