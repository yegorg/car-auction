<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

if (!function_exists('get_categories')) {

	function get_categories() {
		$CI = &get_instance();

		return $CI->db->order_by("category", "ASC")->get("categories")->result();
	}

}

?>