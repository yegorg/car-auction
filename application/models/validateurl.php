<?php
class ValidateURL extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    public function isValidURL($url) {
        
        if($url == NULL OR empty($url)) return false;
        
        $ch = curl_init($url);
        
        if (false === $ch)
        {
                return false;
        }
        
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15") ); // request as if Firefox
        
        $data = curl_exec($ch);
        curl_close($ch);
        
        if($data) return true;
        if(!$data) return false;
        
    }
    
    public function websiteListed($url) {
        $url = $this->dbURLify($url);
        $rs = $this->db->get_where("listings", array("listing_url" => $url));
        
        return count($rs->result());
    }
    
    public function dbURLify($url) {
        return str_replace(array("http://", "https://", "www."), array("","",""), $url);
    }
    
}