<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('get_alexa')) {
	function get_alexa($domain)
    {
      $url = 'http://data.alexa.com/data?cli=10&dat=snbamz&url=http://'.$domain;
      $ch = curl_init();
      $timeout = 5;
      curl_setopt($ch,CURLOPT_URL,$url);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
      curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
      $xml = curl_exec($ch);
      
      
	  if(curl_error($ch)) {
	  	return 'N/A';
	  }

    curl_close($ch);
    
      $xml = simplexml_load_string($xml);
      if(isset($xml->SD[1]->POPULARITY['TEXT']) AND ($xml->SD[1]->POPULARITY['TEXT'] != NULL)) {
        return ($xml->SD[1]->POPULARITY['TEXT']);
      }else{
          return 'N/A';
      }
      
    }
    
    

}

?>