<?php
namespace RedisPageCache;
class MinifyCacheDecorator extends CacheDecorator {
  function set($key,$value) {
    if(isset($value['content'])) {
      $value['content'] = $this->minify($value['content']);
    } else {
      $value = $this->minify($value);
    }
    return $this->Cache->set($key,$value);
  }
  private function minify($content) {
    $replace = array(
      //remove tabs before and after HTML tags
      '/\>[^\S ]+/s'   => '>',
      '/[^\S ]+\</s'   => '<',
      //shorten multiple whitespace sequences; keep new-line characters because they matter in JS!!!
      '/([\t ])+/s'  => ' ',
      //remove leading and trailing spaces
      '/^([\t ])+/m' => '',
      '/([\t ])+$/m' => '',
      // remove JS line comments (simple only); do NOT remove lines containing URL (e.g. 'src="http://server.com/"')!!!
      '~//[a-zA-Z0-9 ]+$~m' => '',
      //remove empty lines (sequence of line-end and white-space characters)
      '/[\r\n]+([\t ]?[\r\n]+)+/s'  => "\n",
      //remove empty lines (between HTML tags); cannot remove just any line-end characters because in inline JS they can matter!
      '/\>[\r\n\t ]+\</s'    => '><',
      //remove "empty" lines containing only JS's block end character; join with next line (e.g. "}\n}\n</script>" --> "}}</script>"
      '/}[\r\n\t ]+/s'  => '}',
      '/}[\r\n\t ]+,[\r\n\t ]+/s'  => '},',
      //remove new-line after JS's function or condition start; join with next line
      '/\)[\r\n\t ]?{[\r\n\t ]+/s'  => '){',
      '/,[\r\n\t ]?{[\r\n\t ]+/s'  => ',{',
      //remove new-line after JS's line end (only most obvious and safe cases)
      '/\),[\r\n\t ]+/s'  => '),',
      //remove quotes from HTML attributes that does not contain spaces; keep quotes around URLs!
      //'~([\r\n\t ])?([a-zA-Z0-9]+)="([a-zA-Z0-9_/\\-]+)"([\r\n\t ])?~s' => '$1$2=$3$4', //$1 and $4 insert first white-space character found before/after attribute
    );
    return preg_replace(array_keys($replace), array_values($replace), $content);
  }
}
