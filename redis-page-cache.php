<?php
namespace RedisPageCache;
/*
Plugin Name: Redis Page Cache
Description: Redis-backed Page Cache
Version: 0.7
Plugin URI: 
Author: Michael McHolm
Author URI: 
*/

if (!defined('ABSPATH')) {
    die();
}
if (defined('WP_INSTALLING') && WP_INSTALLING)
    return;

require "lib/load.php";
if($is_redis_connected && is_admin()) {
  add_action("save_post",array($RedisPageCache,"clear_post"));
}
