<?php
namespace RedisPageCache;
abstract class CacheDecorator extends Cache {
  function __construct(Cache $Cache) {
    $this->Cache = $Cache;
  }
  function has($key) {
    return $this->Cache->has($key);
  }
  function get($key) {
    return $this->Cache->get($key);
  }
  function set($key,$value) {
    return $this->Cache->set($key,$value);
  }
  function delete($key) {
    return $this->Cache->delete($key);
  }
  function flushdb() {
    return $this->Cache->flushdb();
  }
}
