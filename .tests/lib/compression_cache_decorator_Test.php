<?php
namespace RedisPageCache;
require_once ("lib/cache.php");
require_once ("lib/redis_cache.php");
require_once ("lib/cache_decorator.php");
require_once ("lib/compression_cache_decorator.php");

class CompressionCacheDecoratorTest extends \PHPUnit_Framework_TestCase {
  function setup() {
    $this->key = 'key';
    $this->value = 'MESSAGE';
    $this->value_zipped = gzcompress($this->value);
    $this->MockCache = $this->getMockBuilder('\RedisPageCache\RedisCache')
                                  ->disableOriginalConstructor()
                                  ->setMethods(array("set","get"))
                                  ->getMock();
    $this->CacheDecorator = new CompressionCacheDecorator($this->MockCache);
  }
  function testSet() {
    $this->MockCache->expects($this->once())
                     ->method('set')
                     ->with($this->equalTo($this->key),
                            $this->equalTo($this->value_zipped)
                           );
    $this->CacheDecorator->set($this->key,$this->value);
  }
  function testGet() {
    $this->MockCache->expects($this->once())
                     ->method('get')
                     ->with($this->equalTo($this->key))
                     ->will($this->ReturnValue($this->value_zipped));
    $return = $this->CacheDecorator->get($this->key);
    $this->assertEquals($return,$this->value);
  }
}
