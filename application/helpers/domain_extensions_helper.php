<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('domain_extensions')) {

	function domain_extensions( $list_type, $sql_type, $uri_param ) {
		  $CI =& get_instance();
          $settings = $CI->db->query("SELECT SUBSTRING_INDEX(listing_url, '.', -1) AS tld FROM `listings` WHERE list_type = ? GROUP BY tld", array($sql_type));

          if(is_object($settings->row())) {
          	$return ='';

          	foreach ($settings->result() AS $ext) {
          		$return .= '<a href="/'.$list_type.'/view/'.$uri_param.'?domain_extension='.$ext->tld.'">.'.strtoupper($ext->tld).'</a><br/>';
          	}

          	return $return;
          }else{
          	 return 'No listings yet to generate available domain extensions';
          }

          return false;
	}

}

?>