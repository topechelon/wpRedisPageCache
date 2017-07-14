<?php
namespace RedisPageCache;
require_once ("lib/cache.php");
require_once ("lib/redis_cache.php");
require_once ("lib/cache_decorator.php");
require_once ("lib/minify_cache_decorator.php");

class MinifyCacheDecoratorTest extends \PHPUnit_Framework_TestCase {
  function setup() {
    $this->html = <<<EOD
<html>
<head>
<title>
test
</title>
</head>
<body>
Information
</body>
</html>
EOD;
    $this->html_minified = "<html><head><title>test</title></head><body>Information</body></html>";
    $this->key = 'key';
    $this->value = array('response' => 'test',
                         'content' => $this->html);
    $this->value_minified = array('response' => 'test',
                                  'content' => $this->html_minified);
    $this->MockCache = $this->getMockBuilder('\RedisPageCache\RedisCache')
                                  ->disableOriginalConstructor()
                                  ->setMethods(array("set"))
                                  ->getMock();
    $this->CacheDecorator = new MinifyCacheDecorator($this->MockCache);
  }
  function testSet() {
    $this->MockCache->expects($this->once())
                     ->method('set')
                     ->with($this->equalTo($this->key),
                            $this->equalTo($this->value_minified)
                           );
    $this->CacheDecorator->set($this->key,$this->value);
  }
}
