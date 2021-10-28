<?php
class DBTables extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
	
	function show_tables() {
		$query = $this->db->query("SHOW TABLES");
		return $query->result();
	}
	
}