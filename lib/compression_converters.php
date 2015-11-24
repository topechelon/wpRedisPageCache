<?php
namespace RedisPageCache;
class CompressConverter implements Converter {
  function convert($html) {
    return gzcompress($html);
  }
}
class UnCompressConverter implements Converter {
  function convert($html) {
    return gzuncompress($html);
  }
}
