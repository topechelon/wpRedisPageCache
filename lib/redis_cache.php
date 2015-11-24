<?php
namespace RedisPageCache;
class RedisCache {
  protected $log_file = "/tmp/redis_cache.log";
  protected $Redis = null;
  protected $ttl = null;
  protected $compress = true;
  protected $json = true;
  function __construct($Redis,$ttl = 3600,$compress = true,$json = true) {
    $this->Redis = $Redis;
    $this->ttl = $ttl;
    $this->compress = $compress;
    $this->json = $json;
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
    $value = $this->Redis->get($key);
    if($this->compress) {
      $value = gzuncompress($value);
    }
    if($this->json) {
      $value = json_decode($value,true);
    }
    return $value;
  }
  function set($key,$value) {
    if($this->json) {
      $value = json_encode($value);
    }
    if($this->compress) {
      $value = gzcompress($value);
    }
    return $this->Redis->setEx($key,$this->ttl,$value);
  }
}
