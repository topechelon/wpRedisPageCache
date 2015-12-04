<?php
namespace RedisPageCache;
class PageCache {
  protected $Cache = null;
  protected $excluded_scripts = array("/wp-login.php");
  function __construct($Cache) {
    $this->Cache = $Cache;
  }
  function setHash($hash) {
    $this->hash = $hash;
  }
  function getHash() {
    return $this->hash;
  }
  function start_capture() {
    ob_start(array($this,"ob_callback"));
  }
  function clear_post($post_id) {
    $url = get_permalink($post_id);
    $hash = sha1(str_replace(array("http://","https://"),"",$url));
    //$this->Cache->log("clear hash: $hash");
    $this->Cache->delete($hash);
  }
  function ob_callback($content) {
    $hash = $this->getHash();
    if(!is_user_logged_in() && !$this->isExcludedScript()) {
      //$this->Cache->log("set hash: $hash");
      $response_headers = headers_list();
      $save = array("headers" => $response_headers,
                    "content" => $content);
      $this->Cache->set($hash,$save);
    }
    return $content;
  }
  function check_cache() {
    $hash = $this->getHash();
    if(!$this->isExcludedScript() && $this->Cache->has($hash)) {
      //$this->Cache->log("found hash: $hash");
      $cache = $this->Cache->get($hash);
      $this->processHeaders($cache['headers']);
      echo $cache['content'];
      return true;
    }
    return false;
  }
  protected function processHeaders($headers) {
    if(count($headers) > 0) {
      foreach ($headers as $header){
        header($header);
      }
    }
  }
  protected function isExcludedScript() {
    $script = $_SERVER['SCRIPT_NAME'];
    return in_array($script,$this->excluded_scripts);
  }
}
