<?php
class Listings extends CI_Model {

    public $LIMIT = 10;
    public $WHERE = array('listing_status' => 'active', 'sold' => 'N', 'list_type' => 'website');
    public $LIKE = array();
    public $HAVING = array();

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        
    }
    
    private function addQuery() {
         //general query
        $this->db->select("listingID, listing_title, listing_url, bin, CONCAT('$', FORMAT(`bin`,0)) as `starting_`, 
                         site_age, `starting_` as starting_bid, 
                         CONCAT('$', FORMAT(rev_avg,0)) as rev_avg,  
                         list_date,list_expires, 
                         FORMAT(traffic_avg_visits,0) as traffic_avg_visits, pagerank, 
                         PERIOD_DIFF(DATE_FORMAT(NOW(), '%Y%m'), FROM_UNIXTIME(site_age, '%Y%m')) AS diff", false);
        $this->db->from("listings");

    }

    private function addLikeCondition( $type = null ) {

        if(count($this->LIKE)) {
            foreach($this->LIKE as $key => $val) $this->db->like($key, $val, $type);
        }

    }

    private function addHavingCondition() {
        if(count($this->HAVING)) {
            foreach($this->HAVING as $key => $val) $this->db->having($key, $val);
        }
    }
    
    /*
     * Revenue min filter
     */
    function setRevenueFilter($min) {
        $this->WHERE['rev_avg >= '] = $min;
    }
    
    /*
     * Traffic min filter
     */
    function setTrafficFilter($traffic_min) {
        $this->WHERE['traffic_avg_visits >= '] = $traffic_min; 
    }
    
    
    /*
     * Age min filter
     */
     function setAgeFilter($min_age) {
         $this->HAVING['diff >= '] = $min_age;
     }
    
    /*
     * Get featured
     */
    public function getFeaturedListings($start, $count = FALSE) {
        $this->addQuery();
        $this->addLikeCondition();    
        $this->addHavingCondition();

        $this->WHERE['sold']= 'N';
        $this->WHERE['featured'] = 'Y'; 

        $this->db->where($this->WHERE);
        $this->db->where('list_type', 'website');
        $this->db->or_where('list_type', 'domain'); 
        $this->db->order_by("listingID", "DESC");
        
        if(!$count) {
            $this->db->limit($this->LIMIT, $start);
        }
      
        return $this->db->get();
        
    }
    
    /*
     * Get Ending SOon
     */
     public function getEndingSoonListings($start, $count = FALSE) {
        $this->addQuery();    
        $this->addLikeCondition();    
        $this->addHavingCondition();

        $this->db->order_by("list_expires", "ASC") ;
        $this->db->where($this->WHERE);
        
        ($count == FALSE) ? $this->db->limit($this->LIMIT, $start) : '';
        
        return $this->db->get();
        
    }
    
    /*
     * Get NEW Listings
     */
     public function getNewListings($start, $count = FALSE) {
        $this->addQuery();
        $this->addLikeCondition();    
        $this->addHavingCondition();

        $this->db->where($this->WHERE);

            
        $this->db->order_by("listingID", "DESC");
        
        ($count == FALSE) ? $this->db->limit($this->LIMIT, $start) : '';
        
        return $this->db->get();
        
    }
     
     /*
      * Get domain listings
      */
      public function setJustDomains() {
          $this->WHERE['list_type'] = 'domain';        
      }
     
     
    /*
     * Get Just Sold Listings
     */
     public function getJustSoldListings($start, $count = FALSE) {
        $this->addQuery();
        $this->addLikeCondition();    
        $this->addHavingCondition();

        $this->WHERE['sold'] = 'Y';
        $this->db->order_by("sold_date", "DESC") ;
        $this->db->where($this->WHERE);
        ($count == FALSE) ? $this->db->limit($this->LIMIT, $start) : '';
        
        return $this->db->get();
        
    }

     
    /*
     * Get by price
     */ 
     public function getByPrice($start, $filter ,$count = FALSE) {
          $this->addQuery();
          $this->addLikeCondition();    
          $this->addHavingCondition();

          if($filter == 'high-end-price') {
              $this->WHERE['bin >='] = 10000;
          }elseif($filter == 'mid-range-price') {
              $this->WHERE['bin <='] = 10000;
              $this->WHERE['bin >='] = 2500;
          }else{
              $this->WHERE['bin <='] = 1000;
          }        
          
          $this->db->where($this->WHERE);
          
          ($count == FALSE) ? $this->db->limit($this->LIMIT, $start) : '';
          
          return $this->db->get();
         
     }

     public function setMonetizationFilter($key) {
          $this->LIKE['monetization'] = $key;
     }
     

     public function setExtensionFilter($key) {
          $this->LIKE['listing_url'] = $key;
     }
     
     /*
      * Get most active
      */
      public function getMostActive($start, $count = false) {
            $this->addQuery();
            $this->addLikeCondition();    
            $this->addHavingCondition();
            
            $this->db->where($this->WHERE);
            $this->db->select("listingID, listing_title, listing_url, CONCAT('$', FORMAT(`starting_`,0)) as `starting_`, 
                         site_age,
                         CONCAT('$', FORMAT(rev_avg,0)) as rev_avg,  
                         list_date,list_expires,
                         FORMAT(traffic_avg_visits,0) as traffic_avg_visits, pagerank,
                         PERIOD_DIFF(DATE_FORMAT(NOW(), '%Y%m'), FROM_UNIXTIME(site_age, '%Y%m')) AS diff,
                         (select count(*) from bids where bid_listing = listingID) as tBids", false);
            $this->db->order_by('tBids', 'desc');
            
            ($count === false) ? $this->db->limit($this->LIMIT, $start) : '';
        
            return $this->db->get();                        
                
      }
      
     
}