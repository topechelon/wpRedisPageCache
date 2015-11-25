<?php
namespace RedisPageCache;
/*
Plugin Name: Redis Page Cache
Description: Redis-backed Page Cache
Version: 0.5
Plugin URI: 
Author: Michael McHolm
Author URI: 
*/

if (!defined('ABSPATH')) {
    die();
}
if (defined('WP_INSTALLING') && WP_INSTALLING)
    return;

$rps_server = defined("WP_REDIS_HOST") ? WP_REDIS_HOST : '127.0.0.1';
$rps_port = defined("WP_REDIS_PORT") ? WP_REDIS_PORT : '6379';
$rps_ttl = 3600;
$rps_ttl = 600;
$rps_compress = true;
$rps_minify = true;
$Redis = new \Redis();
$is_redis_connected = $Redis->connect($rps_server,$rps_port);
if($is_redis_connected) {
  require "lib/cache.php";
  require "lib/redis_cache.php";
  require "lib/cache_decorator.php";
  require "lib/compression_cache_decorator.php";
  require "lib/json_cache_decorator.php";
  require "lib/minify_cache_decorator.php";
  require "lib/page_cache.php";
  $RedisCache = new RedisCache($Redis,$rps_ttl);
  if($rps_compress) {
    $RedisCache = new CompressionCacheDecorator($RedisCache);
  }
  $RedisCache = new JsonCacheDecorator($RedisCache);
  if ($rps_minify) {
    $RedisCache = new MinifyCacheDecorator($RedisCache);
  }
  $RedisPageCache = new PageCache($RedisCache);
  $uri = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  $key = sha1($uri);
  $RedisPageCache->setHash($key);
  if(is_admin()) {
    add_action("save_post",array($RedisPageCache,"clear_post"));
  } else {
    add_action("plugins_loaded",array($RedisPageCache,"check_cache"));
    add_action("wp_loaded",array($RedisPageCache,"start_capture"));

  }
}
