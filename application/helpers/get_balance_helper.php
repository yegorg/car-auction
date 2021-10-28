<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

if (!function_exists('get_balance')) {

	/**
	 * @param $userID
	 * @return string
	 */
	function get_balance($userID = 'SIGNEDIN') {

		// get balane for this user id
		$CI = &get_instance();

		// set userID
		if ($userID == 'SIGNEDIN') {
			$userID = $CI->session->userdata('loggedIn');
		}

		$CI->db->select_sum("amount");
		$balance = $CI->db->get_where("transactions", [
			'sellerID' => $userID,
			'txStatus' => 'Pending',
		]);

		// return default file if no images found
		if(!is_object($balance->row())) {
			return get_option('currency_symbol') . 0.00;
		}

		// balance info
		$balance = $balance->row();

		return get_option('currency_symbol') . number_format($balance->amount, 0);

	}

}