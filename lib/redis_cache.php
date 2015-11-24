<?php
namespace RedisPageCache;
class RedisCache {
  protected $log_file = "/tmp/redis_cache.log";
  protected $Redis = null;
  protected $ttl = null;
  function __construct($Redis,$ttl = 3600) {
    $this->Redis = $Redis;
    $this->ttl = $ttl;
  }
  function log($info) {
    $log = date("Y-m-d H:i:s - ");
    $log .= $info . "\n";
    file_put_contents($this->log_file,$log,FILE_APPEND);
  }
  function has($key) {
    return $this->Redis->exists($key);
  }
  function get($key) {
    return $this->Redis->get($key);
  }
  function set($key,$value) {
    return $this->Redis->setEx($key,$this->ttl,$value);
  }
}
