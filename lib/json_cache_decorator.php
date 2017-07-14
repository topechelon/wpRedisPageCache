<?php
namespace RedisPageCache;
class JsonCacheDecorator extends CacheDecorator {
  function get($key) {
    $value = $this->Cache->get($key);
    return json_decode($value,true);
  }
  function set($key,$value) {
    $value = json_encode($value);
    return $this->Cache->set($key,$value);
  }
}
