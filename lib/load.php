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
    require "cache.php";
    require "redis_cache.php";
    require "cache_decorator.php";
    require "compression_cache_decorator.php";
    require "json_cache_decorator.php";
    require "minify_cache_decorator.php";
    require "page_cache.php";
  }
  $rps_minify = true;
  $RedisCache = new RedisCache($Redis,REDIS_PAGE_CACHE_TTL);
  $RedisCache = new CompressionCacheDecorator($RedisCache);
  $RedisCache = new JsonCacheDecorator($RedisCache);
  if ($rps_minify) {
    $RedisCache = new MinifyCacheDecorator($RedisCache);
  }
  $RedisPageCache = new PageCache($RedisCache);
}
$cookie_keys = array_keys($_COOKIE);
$is_logged_in = false;
foreach($cookie_keys as $cookie_key) {
  if(preg_match("/^wordpress_logged_in_/",$cookie_key) > 0) {
    $is_logged_in = true;
    break;
  }
}
