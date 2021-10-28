<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('set_option')) {

	function set_option( $option_name, $option_value ) {

		  $CI =& get_instance();
          $settings = $CI->db->get_where("opts", array("option_name" => $option_name));

          if(is_object($settings->row())) {
          	$CI->db->where('id', $settings->row('id'));
          	$CI->db->update("opts", array("option_value" => $option_value));
          }else{
          	$CI->db->insert("opts", array("option_name" => $option_name, "option_value" => $option_value));
          }

          return $CI->db->affected_rows();

	}

}

?>