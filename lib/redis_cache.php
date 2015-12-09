<?php
namespace RedisPageCache;
class RedisCache extends Cache {
  protected $Redis = null;
  protected $ttl = null;
  function __construct($Redis,$ttl = 3600) {
    $this->Redis = $Redis;
    $this->ttl = $ttl;
  }
  function has($key) {
    return $this->Redis->exists($key);
  }
  function get($key) {
    $value = $this->Redis->get($key);
    return $value;
  }
  function set($key,$value) {
    return $this->Redis->setEx($key,$this->ttl,$value);
  }
  function delete($key) {
    return $this->Redis->delete($key);
  }
  function flushdb() {
    return $this->Redis->flushdb();
  }
}
