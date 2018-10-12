<?php

/*
  Class: MyCurl
  Author: Skakunov Alexander (alex.skakunov@gmail.com)
  Date: 26.11.06
  Description: provides a simple tool to GET/POST data with help of CURL library
*/
class MyCurl
{
  protected $_getHeaders = true;//headers will be added to output
  protected $_getContent = true; //contens will be added to output
  protected $_followRedirects = true; //should the class go to another URL, if the current is "HTTP/1.1 302 Moved Temporarily"
  protected $_httpResponseCode;
 
  protected $fCookieFile;
  protected $fSocket;
 
  public function __construct()
  {
    $this->fCookieFile = tempnam("/tmp", "curl_");
    $this->init();
  }

  public function init()
  {
    $this->fSocket = curl_init();
    $this->load_defaults();
    return $this->fSocket;
  }
 
  public function setopt($opt, $value)
  {
    return curl_setopt($this->fSocket, $opt, $value);
  }
 
  public function setGetHeaders($value)
  {
    $this->_getHeaders = !empty($value) ? true : false;
    return $this;
  }
 
  public function setGetContent($value)
  {
    $this->_getContent = !empty($value) ? true : false;
    return $this;
  }
 
  public function setFollowRedirects($value)
  {
    $this->_followRedirects = !empty($value) ? true : false;
    return $this;
  }

  public function getResponseCode()
  {
    return $this->_httpResponseCode;
  }
 
  public function load_defaults()
  {
    $this->setopt(CURLOPT_RETURNTRANSFER, 1);
    $this->setopt(CURLOPT_FOLLOWLOCATION, $this->_followRedirects);
    //$this->setopt(CURLOPT_VERBOSE, true);
    //$this->setopt(CURLINFO_HEADER_OUT, true);
    $this->setopt(CURLOPT_SSL_VERIFYPEER, false);
    $this->setopt(CURLOPT_SSL_VERIFYHOST, false);
    $this->setopt(CURLOPT_HEADER, $this->_getHeaders);
    $this->setopt(CURLOPT_NOBODY, !$this->_getContent);
    $this->setopt(CURLOPT_COOKIEJAR, $this->fCookieFile);
    $this->setopt(CURLOPT_COOKIEFILE, $this->fCookieFile);
    $this->setopt(CURLOPT_USERAGENT, "MyCurl");
    $this->setopt(CURLOPT_POST, 1);
    $this->setopt(CURLOPT_CUSTOMREQUEST,'POST');
  }

  public function __destruct()
  {
    curl_close($this->fSocket);
    unlink($this->fCookieFile);
  }

  public function head($url)
  {
    if(!$this->fSocket)
    {
      throw new Exception('No socket!');
    }
    $this->_getHeaders = true;
    $this->_getContent = false;
    $this->load_defaults();
    $this->setopt(CURLOPT_POST, 0);
    $this->setopt(CURLOPT_CUSTOMREQUEST,'HEAD');
    $this->setopt(CURLOPT_URL, $url);
    $this->setopt(CURLOPT_FILETIME, true);
    $result = curl_exec($this->fSocket);
    $this->_httpResponseCode = curl_getinfo($this->fSocket, CURLINFO_HTTP_CODE);
    $info = curl_getinfo($this->fSocket);
    return $info;
  }

  public function get($url)
  {
    if(!$this->fSocket)
    {
      throw new Exception('No socket!');
    }
    $this->load_defaults();
    $this->setopt(CURLOPT_POST, 0);
    $this->setopt(CURLOPT_CUSTOMREQUEST,'GET');
    $this->setopt(CURLOPT_URL, $url);
    $result = curl_exec($this->fSocket);
    $this->_httpResponseCode = curl_getinfo($this->fSocket, CURLINFO_HTTP_CODE);
    return $result;
  }

  public function post($url, $post_data, $arr_headers=array())
  {

    if(!$this->fSocket)
    {
      throw new Exception('No socket!');
    }
    $this->load_defaults();
    $post_data = $this->_compile_post_data($post_data);
    if(!empty($post_data))
      $this->setopt(CURLOPT_POSTFIELDS, $post_data);

    if(!empty($arr_headers))
      $this->setopt(CURLOPT_HTTPHEADER, $arr_headers);
   
    $this->setopt(CURLOPT_URL, $url);

    $result = curl_exec($this->fSocket);
    $this->_httpResponseCode = curl_getinfo($this->fSocket, CURLINFO_HTTP_CODE);
    return $result;
  }

  protected function _compile_post_data($post_data)
  {
    $o="";
    if(!is_array($post_data)) {
      return $post_data;
    }

    foreach ($post_data as $k=>$v)
      $o.= $k."=".urlencode($v)."&";
    return substr($o,0,-1);
  }
 
  public function get_parsed($result, $bef, $aft="")
  {
    $line=1;
    $len = strlen($bef);
    $pos_bef = strpos($result, $bef);
    if($pos_bef===false)
      return "";
    $pos_bef+=$len;
   
    if(empty($aft))
    { //try to search up to the end of line
      $pos_aft = strpos($result, "\n", $pos_bef);
      if($pos_aft===false)
        $pos_aft = strpos($result, "\r\n", $pos_bef);
    }
    else
      $pos_aft = strpos($result, $aft, $pos_bef);
   
    if($pos_aft!==false)
      $rez = substr($result, $pos_bef, $pos_aft-$pos_bef);
    else
      $rez = substr($result, $pos_bef);
   
    return $rez;
  }

}
