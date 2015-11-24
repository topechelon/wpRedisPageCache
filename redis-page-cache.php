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
$Redis = new \Redis();
$is_redis_connected = $Redis->connect($rps_server,$rps_port);
if($is_redis_connected && !is_admin()) {
  require "lib/redis_cache.php";
  require "lib/page_cache.php";
  require "lib/converter_interface.php";
  require "lib/minify_converter.php";
  require "lib/compression_converters.php";
  $RedisCache = new RedisCache($Redis,$rps_ttl);
  $RedisPageCache = new PageCache($RedisCache,$rps_ttl);
  $RedisPageCache->addHeader("Content-Type: text/html; charset=UTF-8");
  $RedisPageCache->addPreConverter(new MinifyConverter());
  $RedisPageCache->addPreConverter(new CompressConverter());
  $RedisPageCache->addPostConverter(new UnCompressConverter());
  $key = sha1($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  $RedisPageCache->setHash($key);
  add_action("plugins_loaded",array($RedisPageCache,"check_cache"));
  add_action("wp_loaded",array($RedisPageCache,"start_capture"));
}
