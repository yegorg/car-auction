<?php
class UsersModel extends CI_Model {

	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        
    }

    public static function current_username($userID) {

    	$ci = &get_instance();
    	$query = $ci->db->query("SELECT username FROM users WHERE userID = $userID LIMIT 1");

    	if ($query->num_rows() > 0) 
    		return $query->row()->username;
    	else
    		return 'User not found for id #' . $userID;

    }

}