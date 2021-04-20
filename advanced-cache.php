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

if($is_redis_connected && !$RedisPageCache->isUserLoggedIn() && $_SERVER['REQUEST_METHOD'] == 'GET') {
  if(defined('REDIS_PAGE_CACHE_SKIP_URLS')) {
    $skip_urls = preg_split('/,/',REDIS_PAGE_CACHE_SKIP_URLS);
    foreach($skip_urls as $skip_url) {
      if(strpos($_SERVER['REQUEST_URI'],$skip_url) === 0) {
        return;
      }
    }
  }
  $RedisPageCache->setUriHash();
  if($RedisPageCache->check_cache()) {
    exit;
  } else {
    $RedisPageCache->start_capture();
  }
}
