<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('is_user_logged_in')) {
	function is_user_logged_in() {
		  $CI =& get_instance(); 
		  return $CI->session->userdata('loggedIn');
	}
}

?>