<?php
namespace RedisPageCache;
abstract class Cache {
  protected $log_file = "/tmp/redis_page_cache.log";
  function log($info) {
    $log = date("Y-m-d H:i:s - ");
    $log .= $info . "\n";
    file_put_contents($this->log_file,$log,FILE_APPEND);
  }
  abstract function has($key);
  abstract function get($key);
  abstract function set($key,$value);
  abstract function delete($key);
  abstract function flushdb();
}
