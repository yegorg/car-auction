<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Admin extends CI_Controller {

	public $admin_loggedIn;

	/*
		 * Check if logged in or not and assign it to all methods
	*/
	function __construct() {
		parent::__construct();
		$this->admin_loggedIn = $this->session->userdata('admin_loggedIn');
		$this->load->model("UsersModel");
	}

	public function loginas() {

		if (!$this->admin_loggedIn) {
			redirect('/admin');
			exit;
		}

		if (array_key_exists('user', $_GET)) {
			$u = abs(intval($_GET['user']));

			if ($u < 1) {
				die("Invalid user ID");
			}

			$this->session->set_userdata('loggedIn', $u);
			echo '<meta http-equiv="refresh" content="1; url= /users/mylistings" />';

		} else {
			echo 'Invalid request ' . anchor(base_url());
		}

	}

	// Transactions manager
	public function tx(  ) {

		if (!$this->admin_loggedIn) {
			redirect('/admin');
			exit;
		}

		// set all cleared
		if( isset( $_GET[ 'all_cleared' ] ) ) {
			$this->db->query( "UPDATE transactions SET txStatus = 'Cleared' WHERE txStatus = 'Pending'" );
			$data[ 'msg' ] = div_class( $this->db->affected_rows() . ' transactions marked as paid out.', 'alert alert-info');
		}

		// if remove
		if( isset( $_GET[ 'remove' ] ) ) {

			$txID = intval( $_GET[ 'remove' ] );

			// get this info
			$tx = $this->db->select( "txID, auctionID" )
						  ->from( "transactions" )
						  ->where( "txID", $txID)
						  ->get()
						  ->row();

			if( !$tx )
				die( 'NO such transaction' );

			// find and remove this transaction
			$this->db->query( "DELETE FROM transactions WHERE txID = " . $tx->txID );

			// reset listing sold status
			$this->db->query( "UPDATE listings 
			                 SET sold = 'N', sold_price = NULL, 
			                 sold_date = NULL 
			                 	WHERE listingID = " . $tx->auctionID );

			$data[ 'msg' ] = div_class('Transaction removed and listing reactivated.', 'alert alert-info');

		}


		// get transaction list
		$tx = $this->db->select("txID, txDate, amount, originalAmount, txStatus, listingID, paidwith, ref,
		                        listing_title, listing_status, uB.userID AS buyerID, uS.userID AS sellerID, 
		                        uB.email AS buyerEmail, uS.email AS sellerEmail, 
		                        uB.username AS buyerUsername, uS.username as sellerUsername")
			->from('transactions')
			->join('listings', 'auctionID = listingID')
			->join('users uB', 'buyerID = uB.userID')
			->join('users uS', 'sellerID = uS.userID')
			->get()
			->result();

		$data[ 'transactions' ] = $tx;

		$this->load->view( 'admin-transactions', $data );
		
	}

	// Generate Mass Pay File for Paypal
	public function generate_mass_pay(  ) {
		ob_start();
		header('Content-type: text/tab-separated-values');
		header("Content-Disposition: attachment;filename=MassPay".date('jS-F-Y').".txt");

		if (!$this->admin_loggedIn) {
			redirect('/admin');
			exit;
		}

		// In the first column, enter your recipients’ email addresses. 
		// In the second column, enter the payment amounts for each recipient. 
		// In the third column, input the three-letter currency code for the currency of the payment
		// An optional fourth column lets you enter a unique identifier for easier overall record-keeping

		// get transaction list
		$tx = $this->db->select("txID, txDate, amount, listingID, uS.payout_email AS paypal")
			->from('transactions')
			->where("txStatus", "Pending")
			->join('listings', 'auctionID = listingID')
			->join('users uS', 'sellerID = uS.userID')
			->get()
			->result();

		if( !$tx )
			die('No payments to clear.');

		
		foreach ($tx as $payment) {
		    echo $payment->paypal . "\t" . $payment->amount  . "\t" . strtoupper(get_option( 'currency_code' )) . "\t" . "Auction_" . $payment->txID . PHP_EOL;
		}


	}

	// Categories Manager
	public function categories() {

		if (!$this->admin_loggedIn) {
			redirect('/admin');
			exit;
		}

		$action = $this->uri->segment(3);
		$removeID = $this->uri->segment(4);

		$data['action'] = $action;
		$data['id'] = $removeID;

		$data['msg'] = '';

		// if removing category
		if ($removeID and $action and ($action == 'remove')) {
			$id = abs(intval($removeID));
			$this->db->delete("categories", array("catID" => $id));
			redirect('/admin/categories');
		}

		// if adding category
		if ($this->input->post('new_category')) {

			$title = $this->input->post('new_category', 1);
			$slug = create_slug($title);


			// if category doesn't exist already
			$exists = $this->db->select( "catID" )
						  ->from( "categories" )
						  ->where( "slug", $slug)
						  ->get()
						  ->row();

			if( $exists ) {
				$data['msg'] = div_class('Category already exists', 'alert alert-error');
			}else{
				$insert = array('category' => $title, 'slug' => $slug);

				$this->db->insert('categories', $insert);
				$data['msg'] = div_class('Successfully added new category', 'alert alert-success');
			}

		}

		// if updating category
		if ($this->input->post('update_category')) {

			$id = $this->input->post('id', 1);
			$id = (int) $id;
			$title = $this->input->post('update_category', 1);
			$slug = create_slug($title);
			$update = array('category' => $title, 'slug' => $slug);

			$this->db->update('categories', $update, ["catID" => $id]);
			$data['msg'] = div_class('Successfully updated category', 'alert alert-success');

		}

		// get category list
		$data['cats'] = get_categories();

		$this->load->view('admin-categories', $data);
	}

	// SEO Settings
	public function seo() {

		if (!$this->admin_loggedIn) {
			redirect('/admin');
			exit;
		}

		$data = array();

		if (isset($_POST['sb'])) {
			unset($_POST['sb']);
			foreach ($_POST as $k => $v) {
				set_option($k, $v);
			}

			$data['form_message'] = '<div class="alert alert-success">SEO Settings saved.</div>';
		}

		$this->load->view('admin-seo', $data);

	}

	// Configure: website title, logo, twitter url, etc
	public function config() {

		$this->load->library('image_lib');

		if (!$this->admin_loggedIn) {
			redirect('/admin');
			exit;
		}

		$data = array();

		if (isset($_POST['sb'])) {
			unset($_POST['sb']);
			foreach ($_POST as $k => $v) {
				set_option($k, $v);
			}

			$data['form_message'] = '<div class="alert alert-success">Configuration saved.</div>';
		}

		//profile pic
		if (isset($_FILES['file']) AND $_FILES['file']['error'] == 0) {
			//make thumbnail
			$rand = md5(uniqid());
			$ext = explode(".", $_FILES['file']['name']);
			$ext = strtolower(end($ext));

			if (!@getimagesize($_FILES['file']['tmp_name'])) {
				die(_("Invalid picture"));
			}

			$new_image = getcwd() . '/uploads/' . $rand . '.' . $ext;

			if (!move_uploaded_file($_FILES['file']['tmp_name'], $new_image)) {
				echo $this->image_lib->display_errors();
			} else {
				$thephoto = $rand . '.' . $ext;
				set_option('site_logo', $thephoto);
			}
		}

		//homepage header pic
		if (isset($_FILES['header_image']) AND $_FILES['header_image']['error'] == 0) {
			//make thumbnail
			$rand = md5(uniqid());
			$ext = explode(".", $_FILES['header_image']['name']);
			$ext = strtolower(end($ext));

			if (!@getimagesize($_FILES['header_image']['tmp_name'])) {
				die(_("Invalid picture"));
			}

			$new_image = getcwd() . '/uploads/' . $rand . '.' . $ext;

			if (!move_uploaded_file($_FILES['header_image']['tmp_name'], $new_image)) {
				echo $this->image_lib->display_errors();
			} else {
				$thephoto = $rand . '.' . $ext;
				set_option('header_image', $thephoto);
			}
		}

		$this->load->view('admin-config', $data);

	}

	/*
		     * Settings
	*/
	public function settings() {
		if (!$this->admin_loggedIn) {
			redirect('/admin');
			exit;
		}
		$data = array();

		if ($this->input->post('sb')) {
			unset($_POST['sb']);

			$_POST[ 'listing_duration' ] = $_POST[ 'listing_duration_days' ] . ' ' . $_POST[ 'listing_duration_string' ];
			unset( $_POST[ 'listing_duration_days' ], $_POST[ 'listing_duration_string' ] );


			foreach ($_POST as $k => $v) {
				set_option($k, $v);
			}

			$form_message = '<div class="alert alert-success">Settings saved</div>';
			$data['form_message'] = $form_message;
		}

		$ld = get_option( 'listing_duration' );
		$ld = explode( " ", $ld );
		$data['days'] = $ld[ 0 ];
		$data['ds'] = $ld[ 1 ];

		$this->load->view('admin-settings', $data);

	}

	/*
		 * Login admin
	*/
	public function login() {
		if ($this->admin_loggedIn) {
			redirect('/admin');
			exit;
		}

		$data = array();

		if ($this->input->post('sbLogin')) {
			if (!$this->input->post('u') OR !$this->input->post('p')) {
				$data['form_message'] = div_class("username and password are required to login", 'alert alert-error');
			} else {

				if ($this->input->post('u', TRUE) == $this->config->item('admin_user')
					AND md5($this->input->post('p', TRUE)) == $this->config->item('admin_pass')) {
					$this->session->set_userdata("admin_loggedIn", TRUE);
					redirect('/admin');
				} else {
					$data['form_message'] = div_class("Wrong credentials", 'alert alert-error');
				}

			}
		}

		$this->load->view('admin-login', $data);
	}

	/*
		 * Index / Listings Admin
	*/
	public function index() {
		if (!$this->admin_loggedIn) {
			redirect('/admin/login');
			exit;
		}

		$action = $this->uri->segment(3);
		$removeID = $this->uri->segment(4);

		if ($removeID and $action and ($action == 'remove')) {
			$id = abs(intval($removeID));
			$this->db->delete("listings", array("listingID" => $id));
			redirect('/admin');
		}

		if ($removeID and $action and ($action == 'approve')) {
			$id = abs(intval($removeID));
			$this->db->update("listings", array("listing_status" => 'active', 'list_expires' => strtotime("+1 Month")),
				array("listingID" => $id));
			redirect('/admin');
		}

		// manually set featured
		if (isset($_GET['make_featured'])) {
			$featuredID = intval($_GET['make_featured']);

			$this->db->update('listings', array('featured' => 'Y'), array("listingID" => $featuredID));

			$data['message'] = '<div class="alert alert-success">Successfully set listing #' . $featuredID . ' as Featured</div>';

		}

		// manually set featured
		if (isset($_GET['disable_featured'])) {
			$featuredID = intval($_GET['disable_featured']);

			$this->db->update('listings', array('featured' => 'N'), array("listingID" => $featuredID));

			$data['message'] = '<div class="alert alert-info">Listing #' . $featuredID . ' is now "Regular"</div>';

		}

		$dd = $this->db->query("SELECT listingID, listing_title, list_date, featured,
		                  listing_status, sold, sold_date, username, ip, list_uID, list_expires,
		                  ( SELECT COUNT( * ) FROM bids WHERE listings.listingID = bids.bid_listing ) AS tBids
		                  FROM
		                  listings LEFT JOIN users ON listings.list_uID = users.userID
		                  ORDER BY listingID DESC");
		$data['listings'] = $dd->result();

		$this->load->view('admin', $data);
	}

	/*
		 * Log out
	*/
	public function logout() {
		$this->session->unset_userdata('admin_loggedIn');
		redirect('/admin/login');
	}

	/*
		 * Users page
	*/
	function users() {
		if (!$this->admin_loggedIn) {
			redirect('/admin/login');
			exit;
		}

		$removeID = $this->uri->segment(4);

		if ($removeID) {
			$id = abs(intval($removeID));
			$this->db->delete("users", array("userID" => $id));
			$this->db->delete("comments", array("commUser" => $id));
			redirect('/admin/users');
		}

		$this->db->select("users.*, (SELECT COUNT(*) as tUsers FROM users) as tUsers", false);
		$this->db->from("users");
		$this->db->order_by("userID", "DESC");
		$users = $this->db->get();

		$data['users'] = $users->result();
		$this->load->view('admin-users', $data);
	}

	/*
		 * Comments page
	*/
	function comments() {
		if (!$this->admin_loggedIn) {
			redirect('/admin/login');
			exit;
		}

		$removeID = $this->uri->segment(4);

		if ($removeID) {
			$id = abs(intval($removeID));
			$this->db->delete("comments", array("commID" => $id));
			redirect('/admin/comments');
		}

		$this->db->select("comments.*, listings.listingID, listings.listing_title,
		                  users.username, users.ip,
						(SELECT COUNT(*) as tComments FROM comments) as tComments", false);
		$this->db->join("users", "users.userID = comments.commUser", "LEFT");
		$this->db->join("listings", "listings.listingID = comments.listID", "LEFT");
		$this->db->from("comments");
		$this->db->order_by("commID", "DESC");
		$comments = $this->db->get();

		$data['comments'] = $comments->result();
		$this->load->view('admin-comments', $data);
	}

	/*
		 * TOS
	*/
	public function tos() {
		if (!$this->admin_loggedIn) {
			redirect('/admin/login');
			exit;
		}

		if ($this->input->post('sb')) {
			$tospost = $this->input->post('tos');
			$this->db->update("tos", array("tos" => $tospost));
			$data['error'] = div_class('Successfully updated TOS', 'alert alert-success');
		}

		$tos = $this->db->get("tos");
		$tos = $tos->row();
		$data['tos'] = $tos->tos;

		$this->load->view('admin-tos', $data);

	}

	// mail
	public function mail(  ) {

		$data =[  ];

		if (isset($_POST['sb'])) {
			unset($_POST['sb']);
			foreach ($_POST as $k => $v) {
				set_option($k, $v);
			}

			$data['form_message'] = '<div class="alert alert-success">Mail Settings saved.</div>';
		}

		$this->load->view('admin-mail', $data);
	}

	// test mail
	public function testmail() {
		
		// get mail type
		$mail_type = get_option( 'mail_type', 'mail' );

		// get contact email
		$contact_email = get_option( 'contact_email' );

		if( 'mail' == $mail_type ) {

			echo 'This feature is for SMTP server only - with mail() you cannot know if an email is delivered or not. It is up to your host to deliver and your email service to accept it. We strongly recommend to use SMTP for reliability.';

		}elseif( 'smtp' == $mail_type ) {

			// get SMTP mail server details
			$mail_server = get_option( 'smtp_address' );
			$mail_port = get_option( 'smtp_port' );
			$mail_user = get_option( 'smtp_user' );
			$mail_pass = get_option( 'smtp_password' );

			$this->load->library('email');

			$config['protocol'] = 'smtp';
			$config['smtp_host'] = $mail_server;
			$config['smtp_user'] = $mail_user;
			$config['smtp_pass'] = $mail_pass;
			$config['smtp_port'] = $mail_port;
			$config['charset'] = 'utf-8';
			$config['mailtype'] = 'html';
			$config['crlf'] = "\r\n";
			$config['newline'] = "\r\n";

			$this->email->initialize($config);

			$this->email->from($contact_email, 'Me');
			$this->email->to($contact_email, 'Me');

			$this->email->subject('Email Test');
			$this->email->message('Testing the email server.');

			$this->email->send();

			echo $this->email->print_debugger();


		}


	}

}