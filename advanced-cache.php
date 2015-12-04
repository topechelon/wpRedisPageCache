<?php
namespace RedisPageCache;
if (!defined('ABSPATH')) {
    die();
}

if (defined('WP_INSTALLING') && WP_INSTALLING)
    return;
require "lib/load.php";
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
