<?php
namespace RedisPageCache;
if (!defined('ABSPATH')) {
    die();
}

if (defined('WP_INSTALLING') && WP_INSTALLING)
    return;

if ($_SERVER['SCRIPT_NAME'] != '/index.php') {
  return;
}
require "lib/load.php";
$cookie_keys = array_keys($_COOKIE);
$is_logged_in = false;
foreach($cookie_keys as $cookie_key) {
  if(preg_match("/^wordpress_logged_in_/",$cookie_key) > 0) {
    $is_logged_in = true;
    break;
  }
}
if($is_redis_connected && !$is_logged_in) {
  $uri = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  $hash = sha1($uri);
  $RedisPageCache->setHash($hash);
  if($RedisPageCache->check_cache()) {
    exit;
  } else {
    $RedisPageCache->start_capture();
  }
}
