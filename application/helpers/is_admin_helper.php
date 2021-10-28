<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('is_admin')) {
	function is_admin() {
		  $CI =& get_instance(); 
		  return $CI->session->userdata('admin_loggedIn');
	}
}

?>