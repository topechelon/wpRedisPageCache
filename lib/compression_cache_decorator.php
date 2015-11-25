<?php
namespace RedisPageCache;
class CompressionCacheDecorator extends CacheDecorator {
  function get($key) {
    $value = $this->Cache->get($key);
    return gzuncompress($value);
  }
  function set($key,$value) {
    $value = gzcompress($value);
    return $this->Cache->set($key,$value);
  }
}
