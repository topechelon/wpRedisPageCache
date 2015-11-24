<?php
namespace RedisPageCache;
class PageCache {
  const PRE_CACHE = 0;
  const POST_CACHE = 0;
  protected $Cache = null;
  protected $Converters = array();
  function __construct($Cache) {
    $this->Cache = $Cache;
  }
  function setHash($hash) {
    $this->hash = $hash;
  }
  function getHash() {
    return $this->hash;
  }
  function addConverter(Converter $Converter,$WHEN = self::PRE_CACHE) {
    $this->Converters[$WHEN][] = $Converter;
  }
  function start_capture() {
    ob_start(array($this,"ob_callback"));
  }
  function ob_callback($content) {
    $hash = $this->getHash();
    $converted_content = $this->processConverters($content,self::PRE_CACHE);
    //$this->Cache->log("set hash: $hash");
    $response_headers = headers_list();
    $save = array("headers" => $response_headers,
                  "content" => $converted_content);
    $this->Cache->set($hash,$save);
    return $content;
  }
  function check_cache() {
    $hash = $this->getHash();
    if($this->Cache->has($hash)) {
      //$this->Cache->log("found hash: $hash");
      $cache = $this->Cache->get($hash);
      $this->processHeaders($cache['headers']);
      $html = $this->processConverters($cache['content'],self::POST_CACHE);
      echo $html;
      exit;
    }
  }
  protected function processConverters($content,$WHEN) {
    if(isset($this->Converters[$WHEN]) && count($this->Converters[$WHEN]) > 0) {
      foreach($this->Converters[$WHEN] as $Converter) {
        $content = $Converter->convert($content);
      }
    }
    return $content;
  }
  protected function processHeaders($headers) {
    if(count($headers) > 0) {
      foreach ($headers as $header){
        header($header);
      }
    }
  }
}
