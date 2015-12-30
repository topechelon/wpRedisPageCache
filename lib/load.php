<?php
namespace RedisPageCache;

if(!defined('REDIS_PAGE_CACHE_HOST')) {
  define('REDIS_PAGE_CACHE_HOST','localhost');
}
if(!defined('REDIS_PAGE_CACHE_PORT')) {
  define('REDIS_PAGE_CACHE_PORT','6379');
}
if(!defined('REDIS_PAGE_CACHE_DB')) {
  define('REDIS_PAGE_CACHE_DB','0');
}
if(!defined('REDIS_PAGE_CACHE_TTL')) {
  define('REDIS_PAGE_CACHE_TTL','3600');
}
$Redis = new \Redis();
$is_redis_connected = $Redis->connect(REDIS_PAGE_CACHE_HOST,REDIS_PAGE_CACHE_PORT,1);
if($is_redis_connected) {
  $Redis->select(REDIS_PAGE_CACHE_DB);
  if(!class_exists('RedisPageCache\Cache')) {
    $redis_page_cache_dir = dirname(__FILE__);
    require $redis_page_cache_dir . "/cache.php";
    require $redis_page_cache_dir . "/redis_cache.php";
    require $redis_page_cache_dir . "/cache_decorator.php";
    require $redis_page_cache_dir . "/compression_cache_decorator.php";
    require $redis_page_cache_dir . "/json_cache_decorator.php";
    require $redis_page_cache_dir . "/minify_cache_decorator.php";
    require $redis_page_cache_dir . "/page_cache.php";
  }
  $rps_minify = false;
  $RedisCache = new RedisCache($Redis,REDIS_PAGE_CACHE_TTL);
  $RedisCache = new CompressionCacheDecorator($RedisCache);
  $RedisCache = new JsonCacheDecorator($RedisCache);
  if ($rps_minify) {
    $RedisCache = new MinifyCacheDecorator($RedisCache);
  }
  $RedisPageCache = new PageCache($RedisCache);
}
