<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Websites extends CI_Controller {
    
    public function view() {
        
        $this->load->model("UsersModel");
        
        //get URL params
        $filter = $this->uri->segment(3);
        $start = $this->uri->segment(5);
        $start = ($start != 0) ? abs(intval($start-1))*2 : 0;
        
        
        //set default params if not set
        if(!$filter) $filter = 'featured';
        if(!$start) $start = 0;
        
        
        $listings = (object) array();
        $filter_title = _('View Websites For Sale');
        
        //load listings model
        $this->load->model("Listings");
        
        
        //add revenue filter
        if(isset($_GET['revenue_min'])) {
            $rev_min = strip_tags(abs(intval($_GET['revenue_min'])));
            $this->Listings->setRevenueFilter($rev_min);
        }
        
        //add traffic filter
        if(isset($_GET['traffic_min'])) {
            $traffic_min = strip_tags(abs(intval($_GET['traffic_min'])));
            $this->Listings->setTrafficFilter($traffic_min);
        }
        
        //add age filter 
        if(isset($_GET['age_min'])) {
            $age_min = strip_tags(abs(intval($_GET['age_min'])));
            $this->Listings->setAgeFilter($age_min);
        }

        // add monetization filter
        if(isset($_GET['monetization'])) {
            $allowed = array('sales', 'affiliate', 'advertising');
            if(in_array($_GET['monetization'], $allowed)) {
                $this->Listings->setMonetizationFilter($_GET['monetization']);
            }else{
                die('Monetization not recognized');
            }
        }

        // add domain extension filter
        if(isset($_GET['domain_extension'])) {
            $this->Listings->setExtensionFilter($_GET['domain_extension']);
        }
        
        //show listings required by the filter
        switch($filter) {
            
            case "featured":
                $total_items = $this->Listings->getFeaturedListings($start)->num_rows();
                $listings = $this->Listings->getFeaturedListings($start);
                $filter_title = $total_items . _(' websites for sale');
                $uri_param = 'featured';
            break;
            
            case "ending-soon":
                $total_items = $this->Listings->getEndingSoonListings($start)->num_rows();
                $listings = $this->Listings->getEndingSoonListings($start);
                $filter_title = $total_items . _(' websites for sale');
                $uri_param = 'ending-soon';
            break;
            
            case "just-sold":
                $total_items =$this->Listings->getJustSoldListings($start, true)->num_rows();
                $listings = $this->Listings->getJustSoldListings($start);
                $filter_title = $total_items . _(' websites for sale');
                $uri_param = 'just-sold';
            break;
            
            
            case "high-end-price":
            case "mid-range-price":
            case "entry-level-price":
                $total_items = $this->Listings->getByPrice($start, $filter, true)->num_rows();
                $listings = $this->Listings->getByPrice($start, $filter);
                $filter_title = $total_items . _(' websites for sale');
                $uri_param = trim(strip_tags($filter));
            break;
            
            case "new-listings":
            case "all":
                $total_items = $this->Listings->getNewListings($start, true)->num_rows();
                $listings = $this->Listings->getNewListings($start);
                $filter_title = $total_items . _(' websites for sale');
                $uri_param = "new-listings";
            break;
            
            case "domain-only":
                $total_items = $this->Listings->getDomainListings($start, true)->num_rows();
                $listings = $this->Listings->getDomainListings($start);
                $filter_title = $total_items . _(' domains for sale');
                $uri_param = "domain-only";
            break;
            
            case "most-active":
                $total_items = $this->Listings->getMostActive($start, true)->num_rows();
                $listings = $this->Listings->getMostActive($start);
                $filter_title = $total_items . _(' websites for sale');
                $uri_param = "most-active";
            break;
            
        }
        
        $data['uri_param'] = $uri_param;
        $data['listings'] = $listings->result();
        $data['filter_title'] = $filter_title;
        $data['total_pages'] = $total_items/10;
        $data['list_type'] = 'websites';
        $data['seo_title'] = 'Websites  -  ' . get_option('seo_title');

        $this->load->view("websites", $data);
        
    }

    public function gotoadd() {
        ob_start();
        
        $id = $this->uri->segment(3);
        if(!$id) die("Where to go to?");
        $id = abs(intval($id));
        if($id < 1) die("Invalid id");
        
        $q = $this->db->query("SELECT listing_url FROM listings WHERE listingID = $id");
        
        if($q->num_rows()) {
            header("Location: http://" . $q->row()->listing_url);
        }else{
            echo 'Could not find destination URL';
        }
        ob_end_flush();
    }
    
}