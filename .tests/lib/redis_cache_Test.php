<?php
namespace RedisPageCache;
require_once ("lib/cache.php");
require_once ("lib/redis_cache.php");
class RedisCacheTest extends \PHPUnit_Framework_TestCase {
  protected $Redis;
  protected $host = "127.0.0.1";
  protected $port = '6379';
  function setUp() {
    $Redis = new \Redis();
    $Redis->connect($this->host,$this->port);
    $this->RedisCache = new RedisCache($Redis);
  }
  function testSetGetHasDelete() {
    $stuff = "YO YO YO";
    $hash = "TEST_HASH" . date("YmdHis");
    $has = $this->RedisCache->has($hash);
    $this->assertFalse($has);
    $rc = $this->RedisCache->set($hash,$stuff);
    $this->assertTrue($rc);
    $has = $this->RedisCache->has($hash);
    $this->assertTrue($has);
    $value = $this->RedisCache->get($hash);
    $this->assertEquals($value,$stuff);
    $rc = $this->RedisCache->delete($hash);
    $this->assertEquals($rc,1);
    $has = $this->RedisCache->has($hash);
    $this->assertFalse($has);
  }
}
