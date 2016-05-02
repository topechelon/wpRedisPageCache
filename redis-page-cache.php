<?php
namespace RedisPageCache;
/*
Plugin Name: PSI Redis Page Cache
Description: Redis-backed Page Cache
Version: 0.8
Plugin URI: 
Author: Michael McHolm
Author URI: 
Network: True
*/

if (!defined('ABSPATH')) {
    die();
}
if (defined('WP_INSTALLING') && WP_INSTALLING)
    return;
if(empty($RedisPageCache)) {
  require "lib/load.php";
}
if($is_redis_connected && is_admin()) {
  add_action("save_post",array($RedisPageCache,"clear_post"));
  add_action('admin_bar_menu',array($RedisPageCache,"admin_bar_menu"),100);
  add_action('admin_init',array($RedisPageCache,"clear_all"));
}
\register_activation_hook(__FILE__,function() {
  $target = WP_CONTENT_DIR . "/plugins/redis-page-cache/advanced-cache.php";
  $link = WP_CONTENT_DIR . "/advanced-cache.php";
  if(!file_exists($link) && file_exists($target)) {
    @symlink($target,$link);
  }
});
\register_deactivation_hook(__FILE__,function() {
  $link = WP_CONTENT_DIR . "/advanced-cache.php";
  if(file_exists($link)) {
    @unlink($link);
  }
});
