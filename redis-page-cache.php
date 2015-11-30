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

$settings = \get_option('redis_page_cache_connect');
if(!empty($settings)) {
  $rps_server = $settings['host'];
  $rps_port = $settings['port'];
  $rps_ttl = $settings['ttl'];
  $rps_compress = true;
  $rps_minify = true;
  $Redis = new \Redis();
  $is_redis_connected = $Redis->connect($rps_server,$rps_port,1);
} else {
  $is_redis_connected = false;
}
if(is_admin()) {
  require "lib/admin_page.php";
  $AdminPage = new AdminPage($is_redis_connected);
  $AdminPage->init();
}
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
