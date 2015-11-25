<?php
namespace RedisPageCache;
require_once ("lib/cache.php");
require_once ("lib/redis_cache.php");
require_once ("lib/cache_decorator.php");
class MockCacheDecorator extends CacheDecorator {}
class CacheDecoratorTest extends \PHPUnit_Framework_TestCase {
  function setup() {
    $this->key = '1234';
    $this->value = '5678';
    $this->MockCache = $this->getMockBuilder('\RedisPageCache\RedisCache')
                                  ->disableOriginalConstructor()
                                  ->setMethods(array("set","get","has","delete"))
                                  ->getMock();
    $this->Decorator = new MockCacheDecorator($this->MockCache);
  }
  function testSet() {
    $this->MockCache->expects($this->once())
                     ->method('set')
                     ->with($this->equalTo($this->key),
                            $this->equalTo($this->value)
                           );
    $this->Decorator->set($this->key,$this->value);
  }
  function testGet() {
    $this->MockCache->expects($this->once())
                     ->method('get')
                     ->will($this->ReturnValue($this->value))
                     ->with($this->equalTo($this->key));
    $return = $this->Decorator->get($this->key);
    $this->assertEquals($return,$this->value);
  }
  function testHas() {
    $map = array();
    $map[] = array($this->key,true);
    $map[] = array($this->value,false);
    $this->MockCache->expects($this->exactly(2))
                     ->method('has')
                     ->will($this->ReturnValueMap($map));
    $return = $this->Decorator->has($this->key);
    $this->assertTrue($return);
    $return = $this->Decorator->has($this->value);
    $this->assertFalse($return);
  }
  function testDelete() {
    $this->MockCache->expects($this->once())
                     ->method('delete')
                     ->will($this->ReturnValue(1))
                     ->with($this->equalTo($this->key));
    $return = $this->Decorator->delete($this->key);
    $this->assertEquals($return,1);
  }
}
?>
