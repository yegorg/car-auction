<?php
if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Payments extends CI_Controller {

	public $loggedIn;
	public $listingID;
	public $listing;

	function __construct() {
		parent::__construct();
	}

	/*
	****************************************
	WINNER BID PAYMENT
	****************************************
	*/

	// pay won auction via paypal
	public function auction_payments_paypal() {

		$this->load->model('UsersModel');

		$data['viewdata'] = '';
		$this->load->library('CRV_PayPalClass');

		switch ($this->uri->segment(4)) {

		default:

			$loggedIn = $this->session->userdata('loggedIn');

			if( !$loggedIn )
				die( "NOT LOGGED IN" );

			// load url library
			$this->load->helper( 'url' );
			$thisUrl = $this->config->base_url();

			// validate get param
			if( !$this->input->get( 'listing' ) )
				die( 'No listing parameter' );

			$listingID = $this->input->get( 'listing' );

			// get listing info
			$l = $this->db->query( 'SELECT * FROM listings WHERE listingID = ?', [ $listingID ] );
			$l = $l->row(  );

			if( !$l )
				die( 'INVALID LISTING' );


			// setup a current URL variable for this script
			$this_script = $thisUrl . '/payments/auction_payments_paypal';

			ob_start();
			$CRV_PayPalClass = new CRV_PayPalClass;
			$CRV_PayPalClass->add_field('business', get_option('paypal_email'));
			$CRV_PayPalClass->add_field('return', $this_script . '/action/success');
			$CRV_PayPalClass->add_field('cancel_return', $this_script . '/action/cancel');
			$CRV_PayPalClass->add_field('notify_url', $this_script . '/action/ipn');
			$CRV_PayPalClass->add_field('item_name', 'Auction # Payment');
			$CRV_PayPalClass->add_field('amount', $l->wonAmount);
			$CRV_PayPalClass->add_field('currency_code', get_option('currency_code'));
			$CRV_PayPalClass->add_field('custom', $l->listingID);
			$CRV_PayPalClass->add_field('cmd', '_xclick');
			$CRV_PayPalClass->add_field('rm', '2');

			$CRV_PayPalClass->submit_paypal_post();
			// submit the fields to paypal
			$data['viewdata'] = ob_get_clean();

			break;

		case 'success':

			redirect('/users/won');

			break;

		case 'cancel':

			$this->load->view('header');

			$data['viewdata'] = _('Canceled payment');

			break;

		case 'ipn':

			$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
			if (!preg_match('/paypal\.com$/', $hostname)) {
				error_log('Validation post isn\'t from PayPal ' . $hostname);
				exit;
			}

			$body = '';

			if (isset($_POST['payment_status']) AND isset($_POST['txn_type']) AND isset($_POST['custom'])) {

				if ($_POST['payment_status'] == 'Completed') {

					$listingID = intval( $_POST[ 'custom' ] );

					$l = $this->db->query( 'SELECT * FROM listings WHERE listingID = ?', [ $listingID ] );
					$l = $l->row();

					// set original amount
					$amount = $l->wonAmount*get_option('sitefee');
					$amount = $amount/100;
					$amount = $l->wonAmount-$amount;

					// update listing
					$this->db->update('listings', [ 'sold' => 'Y', 
					                  				'sold_date' => time(), 
					                  				'sold_price' => $l->wonAmount, 
					                  				'isPaid' => 'Yes' ], 
					                  				[ 'listingID' => $listingID ]);

					// add transaction
					$transaction = [ 'sellerID'  => $l->list_uID, 
									 'buyerID'   => $l->winnerID, 
									 'auctionID' => $l->listingID, 
									 'txDate'    => time(), 
									 'amount'    => $amount, 
									 'originalAmount' => $l->wonAmount, 
									 'paidwith'   => 'PayPal',
									 'txStatus'   => 'Pending' ];

					$this->db->insert( "transactions", $transaction );


				}

			}

			break;
		}

		$this->load->view('paypal', $data);
		

	} // ./paypal_credits()

	public function auction_payments_stripe() {
		
		if (isset($_POST['stripeToken']) AND !empty($_POST['stripeToken']) AND isset( $_POST[ 'listingID' ] )) {

			$stripe_token = trim(strip_tags($_POST['stripeToken']));

			$listingID = intval( $_POST[ 'listingID' ] );

			$l = $this->db->query( 'SELECT * FROM listings WHERE listingID = ?', [ $listingID ] );
			$l = $l->row();

			$post_params = array('amount' => $l->wonAmount*100,
				'currency' => get_option('currency_code'),
				'source' => $stripe_token,
				'description' => 'Auction #'.$listingID.' Payment');

			//url-ify the data for the POST
			$fields_string = '';
			foreach ($post_params as $key => $value) {$fields_string .= $key . '=' . $value . '&';}
			rtrim($fields_string, '&');

			$ch = curl_init('https://api.stripe.com/v1/charges');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERPWD, get_option('stripe_private'));
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_POST, count($post_params));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

			$output = curl_exec($ch);
			$info = curl_getinfo($ch);

			curl_close($ch);

			$output = json_decode($output);

			if (!$output) {
				die("Could not decode stripe json return");
			}

			if (isset($output->error)) {
				die("Stripe error: " . $output->error->message);
			} else {

				if (isset($output->status) AND ($output->status == 'succeeded')) {
					// set this $listingID as featured

					// update listing
					$this->db->update('listings', [ 'sold' => 'Y', 
					                  				'sold_date' => time(), 
					                  				'sold_price' => $l->wonAmount, 
					                  				'isPaid' => 'Yes' ], 
					                  				[ 'listingID' => $listingID ]);

					// set original amount
					$amount = $l->wonAmount*get_option('sitefee');
					$amount = $amount/100;
					$amount = $l->wonAmount-$amount;

					// add transaction
					$transaction = [ 'sellerID'  => $l->list_uID, 
									 'buyerID'   => $l->winnerID, 
									 'auctionID' => $l->listingID, 
									 'txDate'    => time(), 
									 'amount'    => $amount, 
									 'originalAmount' => $l->wonAmount, 
									 'paidwith'   => 'Stripe',
									 'txStatus'   => 'Pending' ];
									 
					$this->db->insert( "transactions", $transaction );

					echo '<meta http-equiv="refresh" content="0; url=/users/won?justpaid=true "/>';

				} else {

					echo "Sripe payment failed<br/>";
					echo $output->failure_message;

				}

			}

		}

	} // ./stripe_credits()


	/*
	****************************************
	CREDITS PACKS
	****************************************
	*/

	// pack with paypal credits - redirect
	public function paypal_credits() {

		$this->load->model('UsersModel');
		
		if(!$this->session->userdata( 'pack' ))
		   die("Error. Pack id not refered");

		$pack = $this->session->userdata( 'pack' );
		$pack = abs(intval($pack));

		// get cost
		$pack_cost = get_option( 'credits_' . $pack );

		$data['viewdata'] = '';
		$this->load->library('CRV_PayPalClass');

		switch ($this->uri->segment(4)) {

		default:

			$loggedIn = $this->session->userdata('loggedIn');

			if( !$loggedIn )
				die( "NOT LOGGED IN" );

			// setup a current URL variable for this script
			$this_script = 'http://' . $_SERVER['HTTP_HOST'] . '/payments/paypal_credits';

			ob_start();
			$CRV_PayPalClass = new CRV_PayPalClass;
			$CRV_PayPalClass->add_field('business', get_option('paypal_email'));
			$CRV_PayPalClass->add_field('return', $this_script . '/action/success');
			$CRV_PayPalClass->add_field('cancel_return', $this_script . '/action/cancel');
			$CRV_PayPalClass->add_field('notify_url', $this_script . '/action/ipn');
			$CRV_PayPalClass->add_field('item_name', $pack . ' credits pack');
			$CRV_PayPalClass->add_field('amount', $pack_cost);
			$CRV_PayPalClass->add_field('currency_code', get_option('currency_code'));
			$CRV_PayPalClass->add_field('custom', $loggedIn . ':' . $pack . ':' . $pack_cost);
			$CRV_PayPalClass->add_field('cmd', '_xclick');
			$CRV_PayPalClass->add_field('rm', '2');

			$CRV_PayPalClass->submit_paypal_post();
			// submit the fields to paypal
			$data['viewdata'] = ob_get_clean();

			break;

		case 'success':

			redirect('/users/blanace');

			break;

		case 'cancel':

			$this->load->view('header');

			$data['viewdata'] = _('Canceled listing');

			break;

		case 'ipn':

			$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
			if (!preg_match('/paypal\.com$/', $hostname)) {
				error_log('Validation post isn\'t from PayPal ' . $hostname);
				exit;
			}

			$body = '';

			if (isset($_POST['payment_status']) AND isset($_POST['txn_type']) AND isset($_POST['custom'])) {

				if ($_POST['payment_status'] == 'Completed') {

					$custom = explode( ":", $_POST[ 'custom' ] );

					$this->db->update("users", 
					                array("credits" => get_credits()+intval($custom[ 1 ])),
									array("userID" => intval($custom[ 0 ])) );
				}

			}

			break;
		}

		$this->load->view('paypal', $data);
		

	} // ./paypal_credits()

	public function stripe_credits() {
		
		if (isset($_POST['stripeToken']) AND !empty($_POST['stripeToken']) AND isset( $_POST[ 'pack' ] )) {

			$stripe_token = trim(strip_tags($_POST['stripeToken']));

			$pack = $_POST[ 'pack' ];
			$pack = abs(intval($pack));

			// get cost
			$pack_cost = get_option( 'credits_' . $pack )*100;

			if( !$pack_cost ) 
				die( "INVALID_PACK" );

			$post_params = array('amount' => $pack_cost,
				'currency' => get_option('currency_code'),
				'source' => $stripe_token,
				'description' => $pack . ' Credits Pack');

			//url-ify the data for the POST
			$fields_string = '';
			foreach ($post_params as $key => $value) {$fields_string .= $key . '=' . $value . '&';}
			rtrim($fields_string, '&');

			$ch = curl_init('https://api.stripe.com/v1/charges');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERPWD, get_option('stripe_private'));
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_POST, count($post_params));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

			$output = curl_exec($ch);
			$info = curl_getinfo($ch);

			curl_close($ch);

			$output = json_decode($output);

			if (!$output) {
				die("Could not decode stripe json return");
			}

			if (isset($output->error)) {
				die("Stripe error: " . $output->error->message);
			} else {

				if (isset($output->status) AND ($output->status == 'succeeded')) {
					// set this $listingID as featured

					$loggedIn = $this->session->userdata('loggedIn');

					$this->db->update("users", 
					                array("credits" => get_credits()+intval($pack)),
									array("userID" => $loggedIn));

					echo '<meta http-equiv="refresh" content="0; url=/users/balance "/>';

				} else {

					echo "Sripe payment failed<br/>";
					echo $output->failure_message;

				}

			}

		}

	} // ./stripe_credits()


	/*
	****************************************
	BUY IT NOW
	****************************************
	*/

	// Bin Via PayPal
	public function binPaypal() {
		
		try{

		$listingID = $this->session->userdata( 'binListing' );

		// validate listing id	
		if( !$listingID )
			throw new Exception("Error: No listing #ID", 1);

		// get this listing BIN price.
		$price = $this->db->select( "listingID, bin" )
						  ->from( "listings" )
						  ->where( "listingID", $listingID )
						  ->get()
						  ->row();

		// if can't get price, throw error
		if( !$price )				  
			throw new Exception("Error: Couldn't fetch this auction bin price.", 1);
			
		// setup paypal redirect
		$this->load->model('UsersModel');
		
		$data['viewdata'] = '';

		$this->load->library('CRV_PayPalClass');

		switch ($this->uri->segment(4)) {

		default:

			$loggedIn = $this->session->userdata('loggedIn');
			$listingID = $this->session->userdata('binListing');
			$listing = $this->db->get_where("listings", array("listingID" => $listingID))
								 ->row();

			$binAmount = (int) $listing->bin;

			// get latest bid
			$listingID = $listing->listingID;
			$last_bid = $this->db->query("(SELECT amount FROM bids WHERE bid_listing = $listingID
                              ORDER BY bidID DESC LIMIT 1)
                              UNION
                              (SELECT COUNT(bidID) as t_Bids FROM bids WHERE bid_listing = $listingID LIMIT 1)");
			$last_bid = $last_bid->result();

			if (count($last_bid) and $last_bid[0]->amount > 0) {
				$last_bid = $last_bid[0]->amount;

				if( $last_bid > $binAmount )
					$binAmount = (int)$last_bid;
			} 

			// setup a current URL variable for this script
			$this_script = base_url() . 'payments/binPaypal';

			ob_start();
			$CRV_PayPalClass = new CRV_PayPalClass;
			$CRV_PayPalClass->add_field('business', get_option('paypal_email'));
			$CRV_PayPalClass->add_field('return', $this_script . '/action/success');
			$CRV_PayPalClass->add_field('cancel_return', $this_script . '/action/cancel');
			$CRV_PayPalClass->add_field('notify_url', $this_script . '/action/ipn');
			$CRV_PayPalClass->add_field('item_name', 'Auction BIN #' . $listingID);
			$CRV_PayPalClass->add_field('amount', $binAmount);
			$CRV_PayPalClass->add_field('currency_code', get_option('currency_code'));
			$CRV_PayPalClass->add_field('custom', $listingID . ':' . $loggedIn);
			$CRV_PayPalClass->add_field('cmd', '_xclick');
			$CRV_PayPalClass->add_field('rm', '2');

			$CRV_PayPalClass->submit_paypal_post();
			// submit the fields to paypal
			$data['viewdata'] = ob_get_clean();

			break;

		case 'success':

			redirect('/users');

			break;

		case 'cancel':

			$this->load->view('header');

			$data['viewdata'] = _('Canceled listing');

			break;

		case 'ipn':

			$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
			if (!preg_match('/paypal\.com$/', $hostname)) {
				error_log('Validation post isn\'t from PayPal ' . $hostname);
				exit;
			}

			$body = '';

			if (isset($_POST['payment_status']) AND isset($_POST['txn_type']) AND isset($_POST['custom'])) {

				if ($_POST['payment_status'] == 'Completed') {

					// get custom value
					$custom = trim( strip_tags( $_POST[ 'custom' ] ) );

					// explode ( comes in format "$listingID . ':' . $loggedIn" )
					$custom = explode( ':', $custom );


					$listingID = abs(intval($custom[ 0 ]));
					$buyerID   = abs(intval($custom[ 1 ]));

					// get listing info
					$listing = $this->db->get_where("listings", ["listingID" => $listingID])->row();

					// user info
					$user_data = $this->db->get_where( "users", ['userID' => $listing->list_uID])->row();

					$binAmount = (int) $listing->bin;

					// get latest bid
					$listingID = $listing->listingID;
					$last_bid = $this->db->query("(SELECT amount FROM bids WHERE bid_listing = $listingID
		                              ORDER BY bidID DESC LIMIT 1)
		                              UNION
		                              (SELECT COUNT(bidID) as t_Bids FROM bids WHERE bid_listing = $listingID LIMIT 1)");
					$last_bid = $last_bid->result();

					if (count($last_bid) and $last_bid[0]->amount > 0) {
						$last_bid = $last_bid[0]->amount;

						if( $last_bid > $binAmount )
							$binAmount = (int)$last_bid;
					} 

					// update listing status to sold
					$this->db->update("listings", array("sold" => 'Y', 
					                                    "sold_price" => $binAmount,
					                                    "sold_date" => time()), 
					                  array("listingID" => $listingID));

					// set amount for user to see
					$amount = $binAmount*get_option('sitefee');
					$amount = $amount/100;
					$amount = $binAmount-$amount;

					$this->db->update('listings', [ 'winnerID' => $buyerID,
					                  				'isPaid' => 'Yes', 
					                  				'wonAmount' => $binAmount ], 
					                  				[ 'listingID' => $listingID ]);

					// add transaction
					$transaction = [ 'sellerID'  => $user_data->userID, 
									 'buyerID'   => $buyerID, 
									 'auctionID' => $listing->listingID, 
									 'txDate'    => time(), 
									 'amount'    => $amount, 
									 'originalAmount' => $binAmount, 
									 'paidwith'   => 'PayPal',
									 'txStatus'   => 'Pending' ];
					$this->db->insert( "transactions", $transaction );


					// add "bin" into "bids"
					$bin = [ 'bid_date' => time(), 
							 'bid_listing' => $listing->listingID, 
							 'bidder_ID' => $buyerID, 
							 'owner_ID' => $listing->list_uID, 
							 'amount' => $binAmount, 
							 'bid_type' => 'BIN' ];
					$this->db->insert( "bids", $bin );				 

					// create notification
					$noty = [ 'nUser' => $listing->list_uID,
							  'nText' => 'Hey, you\'ve sold your auction. <a href="/users/mylistings">My Listings</a>' ];
					$this->db->insert( 'notifications', $noty );

					// email the listing owner
					$headers = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

					$to = $user_data->email;
					$body = 'Hi there <srong>' . $user_data->username . '</strong>,<br/><br/>';

					$body .= 'You have sold your action. We will process your payment next payout round.<br/>';
					$body .= '<br/><br/>Congratulations!';

					// mail type
					$mail_type = get_option( 'mail_type', 'mail' );

					if( 'mail' == $mail_type ) {

						mail($to, "Auction Sold", $body, $headers);

					}elseif( 'smtp' == $mail_type ){

						// get SMTP mail server details
						$mail_server = get_option( 'smtp_address' );
						$mail_port = get_option( 'smtp_port' );
						$mail_user = get_option( 'smtp_user' );
						$mail_pass = get_option( 'smtp_password' );
						$contact_email = get_option( 'contact_email' );

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

						$this->email->from($contact_email);
						$this->email->to($to);

						$this->email->subject('Auction Sold');
						$this->email->message($body);

						$this->email->send();

					}

				}

			}

			break;
		}

		$this->load->view('paypal', $data);


		}catch( Exception $e ) {

			$this->load->view( 'exceptions', [ 'exception_message' => $e->getMessage() ] );
		}


	}

	// bin with stripe
	public function binStripe(  ) {
			
		if (isset($_POST['stripeToken']) AND !empty($_POST['stripeToken']) AND isset( $_POST[ 'binListing' ] )) {

			// get listing infos
			$listingID = intval($_POST['binListing']);
			$listing = $this->db->get_where("listings", ["listingID" => $listingID])->row();


			$binAmount = (int) $listing->bin;

			// get latest bid
			$listingID = $listing->listingID;
			$last_bid = $this->db->query("(SELECT amount FROM bids WHERE bid_listing = $listingID
                              ORDER BY bidID DESC LIMIT 1)
                              UNION
                              (SELECT COUNT(bidID) as t_Bids FROM bids WHERE bid_listing = $listingID LIMIT 1)");
			$last_bid = $last_bid->result();

			if (count($last_bid) and $last_bid[0]->amount > 0) {
				$last_bid = $last_bid[0]->amount;

				if( $last_bid > $binAmount )
					$binAmount = (int)$last_bid;
			} 

			$stripe_token = trim(strip_tags($_POST['stripeToken']));

			$post_params = array('amount' => $listing->bin * 100,
				'currency' => get_option('currency_code'),
				'source' => $stripe_token,
				'description' => 'Auction BIN #' . $listingID);

			//url-ify the data for the POST
			$fields_string = '';
			foreach ($post_params as $key => $value) {$fields_string .= $key . '=' . $value . '&';}
			rtrim($fields_string, '&');

			$ch = curl_init('https://api.stripe.com/v1/charges');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERPWD, get_option('stripe_private'));
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_POST, count($post_params));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

			$output = curl_exec($ch);
			$info = curl_getinfo($ch);

			curl_close($ch);

			$output = json_decode($output);

			if (!$output) {
				die("Could not decode stripe json return");
			}

			if (isset($output->error)) {
				die("Stripe error: " . $output->error->message);
			} else {

				if (isset($output->status) AND ($output->status == 'succeeded')) {

					// get custom value
					$buyerID   = $this->session->userdata('loggedIn');

					// seller info
					$user_data = $this->db->get_where( "users", ['userID' => $listing->list_uID])->row();

					// update listing status to sold
					$this->db->update("listings", array("sold" => 'Y', 
					                                    "sold_price" => $binAmount, 
					                                    "sold_date" => time() ), 
					                  array("listingID" => $listingID));

					// set amount for user to see
					$amount = $binAmount*get_option('sitefee');
					$amount = $amount/100;
					$amount = $binAmount-$amount;

					$this->db->update('listings', [ 'winnerID' => $buyerID,
					                  				'isPaid' => 'Yes', 
					                  				'wonAmount' => $binAmount ], 
					                  				[ 'listingID' => $listingID ]);

					// add transaction
					$transaction = [ 'sellerID'  => $user_data->userID, 
									 'buyerID'   => $buyerID, 
									 'auctionID' => $listing->listingID, 
									 'txDate'    => time(), 
									 'amount'    => $amount, 
									 'originalAmount' => $binAmount, 
									 'paidwith'   => 'Stripe',
									 'txStatus'   => 'Pending' ];

					$this->db->insert( "transactions", $transaction );


					// add "bin" into "bids"
					$bin = [ 'bid_date' => time(), 
							 'bid_listing' => $listingID, 
							 'bidder_ID' => $buyerID, 
							 'owner_ID' => $listing->list_uID, 
							 'amount' => $binAmount, 
							 'bid_type' => 'BIN'
							];
					$this->db->insert( "bids", $bin );

					// create notification
					$noty = [ 'nUser' => $listing->list_uID,
							  'nText' => 'Hey, you\'ve sold your auction. <a href="/users/mylistings">My Listings</a>' ];
					$this->db->insert( 'notifications', $noty );

					// email the listing owner
					$headers = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

					// mail the seller
					$to = $user_data->email;
					$body = 'Hi there <srong>' . $user_data->username . '</strong>,<br/><br/>';

					$body .= 'You have sold your action. We will process your payment next payout round.<br/>';
					$body .= '<br/><br/>Congratulations!';

					// mail type
					$mail_type = get_option( 'mail_type', 'mail' );

					if( 'mail' == $mail_type ) {

						mail($to, "Auction Sold", $body, $headers);

					}elseif( 'smtp' == $mail_type ) {

						// get SMTP mail server details
						$mail_server = get_option( 'smtp_address' );
						$mail_port = get_option( 'smtp_port' );
						$mail_user = get_option( 'smtp_user' );
						$mail_pass = get_option( 'smtp_password' );
						$contact_email = get_option( 'contact_email' );

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

						$this->email->from($contact_email);
						$this->email->to($to);

						$this->email->subject('Auction Sold');
						$this->email->message($body);

						$this->email->send();

					}

					$this->load->view( 'bin-complete', [ 'listing' => $listing, 'user' => $user_data ] );


				} else {

					echo "Sripe payment failed<br/>";
					echo $output->failure_message;

				}

			}

		}

	}


	// select payment method when publishing regular listing and there's a fee	
	public function selectPaymentMethod(  ) {
		
		return $this->load->view( 'select-payment-method' );

	}

	public function setfeatured() {
		$id = $this->uri->segment(3);
		$id = abs(intval($id));

		if (!$id) {
			die("Error. List id not correct");
		}

		$this->session->set_userdata('listingID', $id);
		redirect('/payments/featured');
	}

	public function relist() {
		$id = $this->uri->segment(3);
		$id = abs(intval($id));

		if (!$id) {
			die("Error. List id not correct");
		}

		$this->session->set_userdata('listingID', $id);

		// if it's free
		$listingID = $this->session->userdata('listingID');

		// check if listing is free
		if (get_option('listing_fee') == 0) {
			$this->db->update("listings",
				array("list_expires" => strtotime("+" . get_option( 'listing_duration' )),
					"listing_status" => "active"),
				array("listingID" => $listingID));

			echo '<meta http-equiv="refresh" content="0; url= /users/mylistings?added=success">';
			exit;

		}

		// if not show payment options
		$this->load->view("relist", array('listingID' => $listingID));

		//redirect('/payments/index');
	}

	// stripe payment LISTING FEE
	public function stripe() {

		if (isset($_POST['stripeToken']) AND !empty($_POST['stripeToken']) AND isset($_POST['listingID'])) {

			$listingID = intval($_POST['listingID']);

			$stripe_token = trim(strip_tags($_POST['stripeToken']));

			$post_params = array('amount' => get_option('listing_fee') * 100,
				'currency' => get_option('currency_code'),
				'source' => $stripe_token,
				'description' => 'Listing fee #' . $listingID);

			//url-ify the data for the POST
			$fields_string = '';
			foreach ($post_params as $key => $value) {$fields_string .= $key . '=' . $value . '&';}
			rtrim($fields_string, '&');

			$ch = curl_init('https://api.stripe.com/v1/charges');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERPWD, get_option('stripe_private'));
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_POST, count($post_params));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

			$output = curl_exec($ch);
			$info = curl_getinfo($ch);

			curl_close($ch);

			$output = json_decode($output);

			if (!$output) {
				die("Could not decode stripe json return");
			}

			if (isset($output->error)) {
				die("Stripe error: " . $output->error->message);
			} else {

				if (isset($output->status) AND ($output->status == 'succeeded')) {
					// set this $listingID as featured

					$this->db->update("listings",
						array("list_expires" => strtotime("+" . get_option( 'listing_duration' )),
							"listing_status" => "active"),
						array("listingID" => $listingID));

					echo '<meta http-equiv="refresh" content="0; url=/users/mylistings?added=true "/>';

				} else {

					echo "Sripe payment failed<br/>";
					echo $output->failure_message;

				}

			}

		}

	}

	// stripe payment FEATURED
	public function stripefeatured() {

		if (isset($_POST['stripeToken']) AND !empty($_POST['stripeToken']) AND isset($_POST['listingID'])) {

			$listingID = intval($_POST['listingID']);

			$stripe_token = trim(strip_tags($_POST['stripeToken']));

			$post_params = array('amount' => get_option('featured_fee') * 100,
				'currency' => get_option('currency_code'),
				'source' => $stripe_token,
				'description' => 'Featured listing #' . $listingID);

			//url-ify the data for the POST
			$fields_string = '';
			foreach ($post_params as $key => $value) {$fields_string .= $key . '=' . $value . '&';}
			rtrim($fields_string, '&');

			$ch = curl_init('https://api.stripe.com/v1/charges');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERPWD, get_option('stripe_private'));
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_POST, count($post_params));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

			$output = curl_exec($ch);
			$info = curl_getinfo($ch);

			curl_close($ch);

			$output = json_decode($output);

			if (!$output) {
				die("Could not decode stripe json return");
			}

			if (isset($output->error)) {
				die("Stripe error: " . $output->error->message);
			} else {

				if (isset($output->status) AND ($output->status == 'succeeded')) {
					// set this $listingID as featured

					$this->db->update("listings",
						array("featured" => "Y"),
						array("listingID" => $listingID));

					echo '<meta http-equiv="refresh" content="0; url=/users/mylistings?added_featured=true "/>';

				} else {

					echo "Sripe payment failed<br/>";
					echo $output->failure_message;

				}

			}

		}

	}

	public function index() {
		
		$this->load->model('UsersModel');
		
		$data['viewdata'] = '';

		$this->load->library('CRV_PayPalClass');

		switch ($this->uri->segment(4)) {

		default:

			// $settings = site_settings();
			$loggedIn = $this->session->userdata('loggedIn');
			$listingID = $this->session->userdata('listingID');
			$listing = $this->db->get_where("listings", array("listingID" => $this->listingID));

			// check if listing is free
			if (get_option('listing_fee') == 0) {
				$this->db->update("listings",
					array("list_expires" => strtotime("+" . get_option( 'listing_duration' )),
						"listing_status" => "active"),
					array("listingID" => $listingID));

				echo '<meta http-equiv="refresh" content="0; url= /users/mylistings?added=success">';
				exit;

			}

			// setup a current URL variable for this script
			$this_script = 'http://' . $_SERVER['HTTP_HOST'] . '/payments/index';

			ob_start();
			$CRV_PayPalClass = new CRV_PayPalClass;
			$CRV_PayPalClass->add_field('business', get_option('paypal_email'));
			$CRV_PayPalClass->add_field('return', $this_script . '/action/success');
			$CRV_PayPalClass->add_field('cancel_return', $this_script . '/action/cancel');
			$CRV_PayPalClass->add_field('notify_url', $this_script . '/action/ipn');
			$CRV_PayPalClass->add_field('item_name', 'Listing Fee');
			$CRV_PayPalClass->add_field('amount', get_option('listing_fee'));
			$CRV_PayPalClass->add_field('currency_code', get_option('currency_code'));
			$CRV_PayPalClass->add_field('custom', $listingID);
			$CRV_PayPalClass->add_field('cmd', '_xclick');
			$CRV_PayPalClass->add_field('rm', '2');

			$CRV_PayPalClass->submit_paypal_post();
			// submit the fields to paypal
			$data['viewdata'] = ob_get_clean();

			break;

		case 'success':

			redirect('/users/mylistings');

			break;

		case 'cancel':

			$this->load->view('header');

			$data['viewdata'] = _('Canceled listing');

			break;

		case 'ipn':

			$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
			if (!preg_match('/paypal\.com$/', $hostname)) {
				error_log('Validation post isn\'t from PayPal ' . $hostname);
				exit;
			}

			$body = '';

			if (isset($_POST['payment_status']) AND isset($_POST['txn_type']) AND isset($_POST['custom'])) {

				if ($_POST['payment_status'] == 'Completed') {

					$listingID = abs(intval($_POST['custom']));
					$this->db->update("listings",
						array("list_expires" => strtotime("+" . get_option( 'listing_duration' )),
							"listing_status" => "active"),
						array("listingID" => $listingID));
				}

			}

			break;
		}

		$this->load->view('paypal', $data);
	}

	public function featured() {
		$this->load->library('CRV_PayPalClass');
		$this->load->model('UsersModel');

		$data['viewdata'] = '';

		switch ($this->uri->segment(4)) {

		default:

			$loggedIn = $this->session->userdata('loggedIn');
			$listingID = $this->session->userdata('listingID');
			$listing = $this->db->get_where("listings", array("listingID" => $this->listingID));

			// setup a current URL variable for this script
			$this_script = 'http://' . $_SERVER['HTTP_HOST'] . '/payments/featured';
			$settings = site_settings();

			ob_start();
			$CRV_PayPalClass = new CRV_PayPalClass;
			$CRV_PayPalClass->add_field('business', get_option('paypal_email'));
			$CRV_PayPalClass->add_field('return', $this_script . '/action/success');
			$CRV_PayPalClass->add_field('cancel_return', $this_script . '/action/cancel');
			$CRV_PayPalClass->add_field('notify_url', $this_script . '/action/ipn');
			$CRV_PayPalClass->add_field('item_name', 'Featured Fee');
			$CRV_PayPalClass->add_field('amount', get_option('featured_fee'));
			$CRV_PayPalClass->add_field('currency_code', get_option('currency_code'));
			$CRV_PayPalClass->add_field('custom', $listingID);
			$CRV_PayPalClass->add_field('cmd', '_xclick');
			$CRV_PayPalClass->add_field('rm', '2');

			$CRV_PayPalClass->submit_paypal_post();

			$data['viewdata'] = ob_get_clean();

			// submit the fields to paypal
			break;

		case 'success':

			redirect('/users/mylistings');

			break;

		case 'cancel':

			$this->load->view('header');

			$data['viewdata'] = _('Canceled listing');

			break;

		case 'ipn':

			$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
			if (!preg_match('/paypal\.com$/', $hostname)) {
				error_log('Validation post isn\'t from PayPal ' . $hostname);
				exit;
			}

			$body = '';

			if (isset($_POST['payment_status']) AND isset($_POST['txn_type']) AND isset($_POST['custom'])) {

				if ($_POST['payment_status'] == 'Completed') {

					$listingID = abs(intval($_POST['custom']));
					$this->db->update("listings",
						array("featured" => "Y"),
						array("listingID" => $listingID));

				}

			}

			break;

		}

		$this->load->view('paypal', $data);
	}

}