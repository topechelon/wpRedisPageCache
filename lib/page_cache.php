<?php
namespace RedisPageCache;
class PageCache {
  protected $Cache = null;
  protected $PreConverters = array();
  protected $PostConverters = array();
  protected $headers = array();
  function __construct($Cache) {
    $this->Cache = $Cache;
  }
  function setHash($hash) {
    $this->hash = $hash;
  }
  function getHash() {
    return $this->hash;
  }
  function addPreConverter(Converter $Converter) {
    $this->PreConverters[] = $Converter;
  }
  function addPostConverter(Converter $Converter) {
    $this->PostConverters[] = $Converter;
  }
  function addHeader($header) {
    $this->headers[] = $header;
  }
  function start_capture() {
    ob_start(array($this,"ob_callback"));
  }
  function ob_callback($content) {
    $hash = $this->getHash();
    $content = $this->processPreConverters($content);
    $this->Cache->log("set hash: $hash");
    $this->Cache->set($hash,$content);
    return $content;
  }
  function check_cache() {
    $hash = $this->getHash();
    if($this->Cache->has($hash)) {
      $this->Cache->log("found hash: $hash");
      $html = $this->Cache->get($hash);
      $this->processHeaders();
      $html = $this->processPostConverters($html);
      echo $html;
      exit;
    }
  }
  protected function processPreConverters($content) {
    if(count($this->PreConverters) > 0) {
      foreach($this->PreConverters as $Converter) {
        $content = $Converter->convert($content);
      }
    }
    return $content;
  }
  protected function processPostConverters($content) {
    if(count($this->PostConverters) > 0) {
      foreach($this->PostConverters as $Converter) {
        $content = $Converter->convert($content);
      }
    }
    return $content;
  }
  protected function processHeaders() {
    if(count($this->headers) > 0) {
      foreach ($this->headers as $header){
        header($header);
      }
    }
  }
}
