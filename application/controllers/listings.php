<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Listings extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model("UsersModel");
	}

	/*
	 * Bid on auction
	*/
	public function bid() {
		$userid = $this->session->userdata("loggedIn");
		if (!$userid) {
			header("Location: /users/login");
		}

		$listingID = $this->uri->segment(3);
		$listingID = abs(intval($listingID));

		if (!$listingID) {
			die(_("Invalid listing ID"));
		}

		$data['listingID'] = $listingID;

		$message = '';

		//if bid
		if ($this->input->post('sb_bid')) {

			$bid_amount = $this->input->post('bid_amount',true);
			$bid_amount = (int) preg_replace('/[^\d+]/', '', $bid_amount);

			if (!$bid_amount || empty($bid_amount) || $bid_amount < 0) {
				$message = _("Bid amount required");
			}

			$data['bid_amount'] = $bid_amount;

			//check that bid amount is at least +5 higher than last bid or starting price
			$this->db->select("amount")
				->from("bids")
				->where("bid_listing", $listingID)
				->order_by('bidID DESC')
				->limit(1);
			$rs = $this->db->get();

			if ($rs->num_rows()) {
				$min_bid = $rs->row()->amount + 5;
			} else {
				$this->db->select("starting_")->from("listings")->where("listingID", $listingID);
				$min_bid = $this->db->get();
				if ($min_bid->num_rows()) {
					$min_bid = $min_bid->row()->starting_ + 5;
				} else {
					$message = _('Could not find a starting bid for this listing.');
				}
			}

			if ($min_bid > $bid_amount) {
				$message = _('This bid should be at least of ') . get_option('currency_symbol') . number_format($min_bid, 0);
			} else {
				if ($this->uri->segment(4) && $this->uri->segment(4) == 'confirm') {

					$rs = $this->db->query("SELECT list_uID FROM listings WHERE listingID = ?", [$listingID]);
					$ownerID = $rs->row()->list_uID;

					if ($rs->row()->list_uID == $userid) {

						$message = _('Do not bid on your own listings');

					} else {

						$bidArray = ['bid_date' => time(), 'bid_listing' => $listingID,
							'bidder_ID' => $userid, 'owner_ID' => $ownerID,
							'amount' => $bid_amount];
						if ($this->db->insert("bids", $bidArray)) {
							$message = _('Bid confirmed, thank you.');
							#$message .= '<br/>' . $this->db->last_query();

							// add notification
							$noty = [ 'nUser' => $ownerID,
									  'nText' => 'Hey, you\'ve got a new bid. <a href="/users/offers">My Offers</a>' ];
							$this->db->insert( 'notifications', $noty );

							// email the listing owner
							$headers = 'MIME-Version: 1.0' . "\r\n";
							$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

							$query = $this->db->query("SELECT username, email FROM users WHERE userID = ? LIMIT 1", [$ownerID]);
							$user_data = $query->row();

							$to = $user_data->email;

							$body = 'Hi there <srong>' . $user_data->username . '</strong>,<br/><br/>';

							$body .= 'You have received a new bid of ' . $bid_amount . ' to your listing:<br/>';
							$body .= '<a href="' . base_url() . 'auctions/' . $listingID . '-new-bid-received">' . base_url() . '/auctions/' . $listingID . '-new-bid-received</a>';

							$body .= '<br/><br/>Please login to view full bid details!';

							$mail_type = get_option( 'mail_type', 'mail' );

							if( 'mail' == $mail_type ) {
								mail($to, "New Bid Received", $body, $headers);
							} elseif( 'smtp' == $mail_type  ) {

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

								$this->email->subject('New Bid Received');
								$this->email->message($body);

								$this->email->send();

							}

						} else {
							$message = _('DB Error!');
						}
					}
				}
			}

		} //if bin
		elseif ($this->input->post('sb_bin')) {
			$this->db->select("bin")->from("listings")->where("listingID", $listingID);
			$min_bid = $this->db->get();
			if ($min_bid->num_rows()) {
				$min_bid = $min_bid->row()->bin;
				$data['bid_amount'] = $min_bid;
			} else {
				$message = _('Could not find a starting bid for this listing.');
			}
		} else {
			$message = _("Bid or bin required to view this page");
		}

		$data[ 'seo_title' ] = 'Bid on Auction';

		$data['message'] = $message;
		$this->load->view('bid', $data);

	}


	/*
	 * BIN BIN BIN!
	 */
	// GET /listings/bin-[ :id ]-[ :title ]
	public function bin( $id, $title ) {
		
		try {


		// validate it & title
		if( !$id || !$title )
			throw new Exception("Error Processing Request", 1);
		
		// validate logged in user
		if( !$this->session->userdata( 'loggedIn' ) ) {
			redirect( "/?login=true" );
			exit;
		}

		// set userid
		$userid = $this->session->userdata( 'loggedIn' );

		// validate listing
		$listing = $this->db->get_where( "listings", [ "listingID" => $id ] )->row();

		if( !$listing )
			throw new Exception("Error: Listing you're trying to bid on does not exist.", 1);

		// check not to bid on own listings
		if( $listing->list_uID == $userid )
			throw new Exception("Error: Dont't bid on your own listings.", 1);
			

		// setup payment terms and redirect to payment
		if( isset( $_GET[ 'setbin' ] ) ) {

			$this->session->set_userdata('binListing', $listing->listingID);
			redirect( "/payments/binPaypal" );

		}else{

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


			$this->load->view( 'select-bin-method', [ 'listing' => $listing, 'seo_title' => 'Buy It now - ' . $listing->listing_title, 'binAmount' => $binAmount ] );
		}

		}catch( Exception $e ) {

			$this->load->view( 'exceptions', [ 'exception_message' => $e->getMessage() ] );
		}
			

	}

	/*
	 * Listing details page
	*/
	public function index($id, $title) {
		$userid = $this->session->userdata("loggedIn");

		$this->load->helper("form");

		$listingID = (int) $id;


		if (!$listingID) {
			return $this->load->view('listing-not-found');
		}

		//get listing details
		$this->db->select("listings.*, userID, username, photo, category, slug");
		$this->db->from("listings");
		$this->db->where(["listingID" => $listingID]);
		$this->db->join("users", "listings.list_uID = users.userID");
		$this->db->join("categories", "listings.list_catID = categories.catID");
		$l = $this->db->get()->row();


		if (!$l) {
			return $this->load->view('listing-not-found');
		}

		if ($l->listing_status != "active") {
			return $this->load->view('inactive-listing');
		}

		if ($l->list_expires < time()) {
			$data['hide_bid'] = 'true';
		}

		if (is_object($l)) {

			//get latest bid
			$last_bid = $this->db->query("SELECT amount,  

			                             (SELECT COUNT(bidID) FROM bids 
			                             WHERE bid_listing = $listingID) as t_Bids 

			                             FROM bids 
			                             WHERE bid_listing = $listingID 
			                             ORDER BY bidID DESC LIMIT 1");
			$last_bid = $last_bid->row();

			if (is_object($last_bid) AND $last_bid->amount > 0) {

				$bids_count = $last_bid->t_Bids;
				$last_bid_plus = get_option('currency_symbol') . number_format($last_bid->amount + 5, 0);
				$lstatus = ($last_bid->amount >= $l->reserve) ? _('Reserve Met') : _('Reserve Not Met');

				if ($last_bid->amount >= $l->bin) {
					$lstatus = _('Received BIN');
				}

				$last_bid = get_option('currency_symbol') . number_format($last_bid->amount, 0);
			} else {
				$last_bid = get_option('currency_symbol') . number_format($l->starting_, 0);
				$last_bid_plus = get_option('currency_symbol') . number_format($l->starting_ + 5, 0);
				$bids_count = 0;
				$lstatus = _('Reserve Not Met');
			}

			if ($l->sold == 'Y') {
				$data['hide_bid'] = 'true';
				$lstatus = _('Sold on ') . date("jS F Y", $l->sold_date);
			}

			//get comments
			$this->db->select("commID, comment, comm_date, userID, username");
			$this->db->from("comments");
			$this->db->where("listID = $listingID");
			$this->db->join('users', 'comments.commUser = users.userID');
			$comments = $this->db->get();

			$data['last_bid'] = $last_bid;
			$data['bid_count'] = $bids_count;
			$data['last_bid_plus'] = $last_bid_plus;
			$data['lstatus'] = $lstatus;
			$data['owns_listing'] = ($l->list_uID == $userid) ? 'yes' : 'no';

			$data['l'] = $l;

			if ($comments->num_rows()) {
				$data['comments'] = $comments;
			}

			//get attachments
			$att = $this->db->get_where("attachments", ["listID" => $listingID]);
			$data['att'] = $att->result();

			// get all bids
			// $this->db->select('*')->from('members')->join('membership', 'membership.id=members.id')->where($where)->get();

			$all_bids = $this->db->select('bids.bid_date, bids.amount, users.userID, users.username')
				->from('bids')
				->join('users', 'bids.bidder_ID = users.userID')
				->where(['bids.bid_listing' => $listingID])
				->get();


			$data['all_bids'] = $all_bids;

			$data[ 'seo_title' ] = $l->listing_title . ' Live Auction';

			return $this->load->view('single-listing', $data);

		} else {
			return $this->load->view('listing-not-found', [ 'seo_title' => 'Listing Not Found' ]);
		}

	}

	/*
		     * Leave comments to movies
	*/
	public function ajax_comment() {

		$userID = is_user_logged_in();

		if ($userID) {

			foreach ($this->input->post() as $k => $v) {
				if ($this->input->post($k, true) == "") {
					print '<div class="alert alert-warning">';
					print _('All fields are mandatory');
					print '</div>';
					exit;
				}
			}

			$comment = [];
			$comment['comm_date'] = time();
			$comment['commUser'] = $userID;
			$comment['listID'] = abs(intval($this->input->post('listID', true)));
			$comment['comment'] = trim(strip_tags($this->input->post('comment', true)));

			if (strlen($comment['comment']) < 10) {
				echo div_class(_('Please enter at least 10 characters for your comment'), 'alert alert-error');
				exit;
			}

			if ($this->db->insert("comments", $comment)) {
				echo div_class(_('Thank you for your comment'), 'alert alert-warning');
				echo '<script type="text/javascript">';
				echo '$(function() {';
				echo '$("#comment-form").hide("slow");';
				echo '})';
				echo '</script>';
			} else {
				echo div_class('DB Error!', "alert alert-error");
			}

		} else {
			echo '<div class="alert alert-error">Please login</div>';
		}
	}

	/*
		     * Load latest comment via ajax
	*/
	function ajax_last_comment() {
		$lastID = abs(intval($this->input->post("last", true)));
		$movID = abs(intval($this->input->post("movie", true)));

		if ($lastID AND $movID) {

			//get comments
			$this->db->select("commID, comment, comm_date, userID, username");
			$this->db->from("comments");
			$this->db->where("commID > $lastID");
			$this->db->where("listID = $movID");
			$this->db->join('users', 'comments.commUser = users.userID');
			$comments = $this->db->get();
			$comments = $comments->result();

			if (count($comments)) {
				foreach ($comments as $c) {
					echo '<li data-lastID="' . $c->commID . '">';
					?>
                    <span class="comment_author"><b
                                class="icon-user"></b> <?php echo anchor('users/profile/' . url_title($c->username), $c->username); ?>
                        on <b class="icon-calendar"></b><em><?php echo date("jS F Y H:ia", $c->comm_date); ?></em></span>
                    <div class="comment_content"><?php echo wordwrap($c->comment, 80, '<br/>', true); ?></div>
                    <?php
echo '</li>';
				}
			}

		} else {

		}

	}

	/*
		     * Remove a comment
	*/
	function remove_c() {
		$id = $this->uri->segment(3);
		$id = abs(intval($id));

		if (!$id) {
			die("ID?");
		}

		$userID = $this->session->userdata("loggedIn");
		$userID = abs(intval($userID));

		if (!$userID) {
			die("Login first");
		}

		$listID = $this->uri->segment(4);
		$listID = abs(intval($listID));

		if (!$listID) {
			die("Listing ID?");
		}

		$ownsListing = $this->db->get_where("listings", ["listingID" => $listID, "list_uID" => $userID]);

		#echo $this->db->last_query() . '<br/>';

		if ($ownsListing->num_rows()) {
			$this->db->delete("comments", ["commID" => $id, "listID" => $listID]);
			echo "ok";
		} else {
			die("You dont own the listing");
		}

	}

	/* 
	 * Manual Payment
	*/
	public function manualpayment() {
		
		// get listing id
		if( !$this->input->get( 'listing' ) )
			die( "Invalid listing id" );

		// get transfer type
		if( !$this->input->get( 'type' ) )
			die( "Invalid transfer" );

		// validate transfer type
		$transferType = $this->input->get( 'type' );
		if( !in_array( $transferType, array( 'bank-transfer', 'cash' )))
			die( "Invalid transfer type" );

		// set listing id
		$listingID = $this->input->get( 'listing' );
		$listingID = intval( $listingID );

		// get listing info ( to validate entry )
		$listing = $this->db->get_where( "listings", [ "listingID" => $listingID ] )->row();

		if( !$listing )
			die("Error: Listing you're trying to bid on does not exist.");

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

		if( isset( $_POST[ 'reference' ] ) ) {

			if( empty( $_POST[ 'reference' ] ) )
				die( 'Insert reference' );

			$data[ 'transferType' ] = $transferType;
			$data[ 'transactionRef' ] = trim( strip_tags( $_POST[ 'reference' ] ) );

			$paidWith = ( $transferType == 'bank-transfer' ) ? 'Bank' : 'Cash';

			// insert transaction
			$listingID = $listing->listingID;
			$buyerID   = $this->session->userdata("loggedIn");
			$userid = $this->session->userdata("loggedIn");

			// user info
			$user_data = $this->db->get_where( "users", ['userID' => $listing->list_uID])->row();

			// update listing status to sold
			$this->db->update("listings", array("sold" => 'Y', "sold_price" => $binAmount,"sold_date" => time()), 
			                  array("listingID" => $listingID));

			// set amount for user to see
			$amount = $binAmount*get_option('sitefee');
			$amount = $amount/100;
			$amount = $binAmount-$amount;

			// add transaction
			$transaction = [ 'sellerID'  => $user_data->userID, 
							 'buyerID'   => $buyerID, 
							 'auctionID' => $listing->listingID, 
							 'txDate'    => time(), 
							 'amount'    => $amount, 
							 'originalAmount' => $binAmount, 
							 'paidwith'   => $paidWith,
							 'txStatus'   => 'Pending', 
							 'ref'       => $data['transactionRef'] ];
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

			// compose message
			$message = 'Hi there <srong>admin</strong>,<br/><br/>';
			$message .= 'You have received a new <strong>'.$transferType.'</strong> reference to listing: <strong>'.$listing->listing_title.'</strong><br/>';
			$message .= '';

			$message .= '<br/><br/>Please login to admin area to see full details!';

			$mail_type = get_option( 'mail_type', 'mail' );

			if( 'mail' == $mail_type ) {
				
				// mail admin
				mail( $this->config->item( 'admin_email' ), 'New ' . $transferType, $message, $headers);

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
				$this->email->to($contact_email);

				$this->email->subject('New ' . $transferType);
				$this->email->message($message);

				$this->email->send();

			}

			$this->load->view( 'manual-transaction-message', $data );

		}else{

			$data[ 'transferType' ] = $transferType;
			$data[ 'listingID' ]    = $listing->listingID;
			$data[ 'listing' ]      = $listing;

			$this->load->view( 'manual-transaction', $data );

		}

	}
	
}