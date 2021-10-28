<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Auctions extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model("AuctionsModel");
	}

	// GET /auctions/:category/:type ( Latest/Featured/Ending/Hot )
	public function index($category, $type) {

		if (!$category || !$type) {
			redirect('/auctions/all/latest');
			exit;
		}

		// set category
		if (!$category) {
			throw new Exception("Error Processing Request: Category not set", 1);
		}

		// santize type
		$type = trim(strip_tags($type));
		$category = trim(strip_tags($category));

		// set breadcrumbs
		$breadcrumbs = array(base_url() => 'Home');

		// get category
		if ($category == 'all') {

			$data['category'] = ucfirst($type);
			$data['category_slug'] = 'all';
			$data['filter_category'] = '';
			$showCatID = null;
			$breadcrumbs[base_url() . 'auctions/all/' . $type] = 'All Auctions';
			$breadcrumbs['#'] = ucfirst($type);

			$data[ 'seo_title' ] = ucfirst($type) . ' Auctions';

		} else {

			$slug = trim(strip_tags($category));
			$category = $this->db->get_where('categories', array('slug' => $category))->row();
			$data['category_slug'] = $category->slug;

			$showCatID = $category->catID;
			$data['category'] = ucfirst($type) . ' ' . $category->category;
			$data['filter_category'] = $category->category;

			$breadcrumbs[base_url() . 'auctions/' . $category->slug . '/latest'] = $category->category . ' Auctions';

			$breadcrumbs[] = ucfirst($type);

			$data[ 'seo_title' ] = $data[ 'category' ] . ' Auctions';

		}

		// order fields
		switch ($type) {

			case 'featured':
			case 'latest':
				$orderField = 'listingID';
				$orderType = 'DESC';
				break;

			case 'ending':
				$orderField = 'listingID';
				$orderType = 'ASC';
				break;

			case 'hot':
				$orderField = 'hotness'; // @TODO HOT SORT
				$orderType = 'DESC'; // @TODO HOT SORTING
				break;

		}

		// add pricing into ordering field
		if( $this->input->get( 'price' ) ) {
			$price = $this->input->get( 'price',true );

			if( $price == 'asc' OR $price == 'desc' ) {				
				$existentOrderField = $orderField . ' ' . $orderType;
				$orderField = 'bin ' . strtoupper($price);
				$orderField .= ',' . $existentOrderField;
				$orderType = '';
			}
		}

		$extraANDSQL = '';
		$params = [];

		// set category from URL
		if ($showCatID != null) {
			$extraANDSQL .= ' AND list_catID = ' . $showCatID . '';
		}

		// is it featured
		if ($type == 'featured') {
			$extraANDSQL = ' AND featured = "Y" ';
		}

		// set search term if ANY
		if (isset($_GET['search_term'])) {
			$searchTerm = trim(strip_tags($_GET['search_term']));
			$searchTerm = (string) $searchTerm;

			if (!empty($searchTerm)) {
				$extraANDSQL .= ' AND (listing_title LIKE ? OR listing_description LIKE ?) ';
				$params[] = '%' . $searchTerm . '%';
				$params[] = '%' . $searchTerm . '%';

				$data['category'] = "Search: \"$searchTerm\"";
				$data[ 'seo_title' ] = "Auctions Matching Term: \"$searchTerm\"";

				// set breadcrumbs
				$breadcrumbs = array(base_url() => 'Home');
				$breadcrumbs[] = 'Search Results';

			}

		}

		$listings = $this->db->query("SELECT `listingID`, `listing_title`, `list_date`, `list_expires`,
                                     `bin`, `category`, `slug`, `catID`,
                                     (SELECT COUNT(bids.bidID) FROM bids WHERE listingID = bid_listing) AS hotness,

                                     (
                                      CASE
                                      	WHEN ( SELECT COUNT(*) FROM bids WHERE bid_listing = listingID )
                                      	THEN
                                      		(SELECT amount FROM bids WHERE bid_listing = listingID ORDER BY bidID DESC LIMIT 1)
                                      	ELSE
                                      		starting_
                                      	END
                                      ) AS `starting_`

                                     FROM (`listings`)

                                     JOIN `categories` ON `listings`.`list_catID` = `categories`.`catID`

                                     WHERE `listing_status` = 'active'
                                     AND `sold` = 'N'
                                     AND `list_expires` > " . time() . "

                                     $extraANDSQL

                                     ORDER BY $orderField $orderType", $params);

		// echo $this->db->last_query();

		$data['listings'] = $listings->result();

		$data['breadcrumbs'] = $breadcrumbs;

		$this->load->view('auctions', $data);
	}

}