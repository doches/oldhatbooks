<?php

class Page
{
  var $title="";
  var $blob="";
  var $body="";
  var $head="";
  var $template;
  
  var $slots = Array("%TITLE%","%BLOB%","%BODY%","%HEAD%");
  
  function Page($filename) {
    $this->template = $filename;
  }
  
  function to_s() {
    $str = file_get_contents($this->template);
    $str = str_replace($this->slots,Array($this->title,$this->blob,$this->body,$this->head),$str);
    return $str;
  }
}

?>
