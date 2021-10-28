<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('get_pagerank')) {
    
    function get_pagerank($domain)
    {
      $url = 'http://honorcoders.com/prapi/?key=jDrRUmtJjJgdW0p&url=http://'.$domain;
	  $pr = @file_get_contents($url);
      if($pr) {
      	return $pr; 
      }else{
      	return '0';
      }
    }
    
    

}

?>