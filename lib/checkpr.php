<?php

function getpr($url) {
  $alexahost='data.alexa.com';
  $alexaua='Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0';
  $fp = fsockopen($alexahost, 80, $errno, $errstr, 30);
  if ($fp) {
    $out = "GET /data?cli=10&dat=snbamz&url=$url HTTP/1.1\r\n";
    $out .= "User-Agent: $alexaua\r\n";
    $out .= "Host: $alexahost\r\n";
    $out .= "Connection: Close\r\n\r\n";

    fwrite($fp, $out);

    $data = '';
    while (($line = fgets($fp, 4096)) !== false) {
        $data .= $line;
    }
    fclose($fp);

    $re = '/< *POPULARITY[^>]*TEXT *= *["\']?([^"\']*)/i';
    preg_match($re, $data, $matches);

    if(isset($matches[1])){
      return intval($matches[1]);
    }
    return false;
  }
}
