<?php
namespace RedisPageCache;
/*
Plugin Name: Redis Page Cache
Description: Redis-backed Page Cache
Version: 0.1
Plugin URI: 
Author: Michael McHolm
Author URI: 
*/

if (!defined('ABSPATH')) {
    die();
}
if (defined('WP_INSTALLING') && WP_INSTALLING)
    return;

$rps_server = '127.0.0.1';
$rps_port = 6379;
$rps_ttl = 3600;
$rps_ttl = 600;
$rps_compress = true;
$rps_minify = true;
$rps_minify = true;
$Redis = new \Redis();
$is_redis_connected = $Redis->connect($rps_server,$rps_port);
if($is_redis_connected && !is_admin()) {
  require "lib/redis_cache.php";
  require "lib/page_cache.php";
  require "lib/converter_interface.php";
  require "lib/minify_converter.php";
  $RedisCache = new RedisCache($Redis,$rps_ttl,$rps_compress);
  $RedisPageCache = new PageCache($RedisCache);
  if ($rps_minify) {
    $RedisPageCache->addConverter(new MinifyConverter());
  }
  $key = sha1($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  $RedisPageCache->setHash($key);
  add_action("plugins_loaded",array($RedisPageCache,"check_cache"));
  add_action("wp_loaded",array($RedisPageCache,"start_capture"));
}
