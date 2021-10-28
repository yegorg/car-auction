<?php
class Stats extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function listings_open() {
        
        $this->db->select("listingID");
        $this->db->from("listings");
        $this->db->where(array("sold" => "N", "listing_status" => "active"));
        $rs = $this->db->get();
        
        return $rs->num_rows();
    }
    
    function members_count() {
        $this->db->select("userID");
        $this->db->from("users");
        $rs =$this->db->get();
        
        return $rs->num_rows();
    }
    
    function count_bids() {
        $week_before = strtotime("-1 Week");
        $this->db->select("bidID");
        $this->db->from("bids");
        $this->db->where("bid_date >=", $week_before);
        $rs = $this->db->get();
       
        
        return $rs->num_rows();
    }
    
    function sales_last_month() {
        $last_month = date("M Y", strtotime("last month"));
        
        $this->db->select("CONCAT('$', FORMAT(SUM(sold_price),0)) AS am", false);
        $this->db->from("listings");
        $this->db->where("FROM_UNIXTIME(sold_date, '%b %Y') = ", $last_month);
        
        $rs = $this->db->get();
        
        if($rs->num_rows()) {
            $rs = $rs->row();
            return $rs->am;
        }else{
            return '$' . 0.00;
        }
        
    }
    
    function sales_overall() {
        $this->db->select("CONCAT('$', FORMAT(SUM(sold_price),0)) AS am", false);
        $this->db->from("listings");
        $rs = $this->db->get();
        

        if($rs->num_rows()) {
            $rs = $rs->row();
            return $rs->am;
        }else{
            return '$' . 0.00;
        }
        
    }
    
}