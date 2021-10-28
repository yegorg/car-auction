<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Users extends CI_Controller {

	public $loggedIn;

	/*
		     * Check if logged in or not and assign it to all methods
	*/
	function __construct() {
		parent::__construct();
		$this->loggedIn = $this->session->userdata('loggedIn');
		$this->load->model("UsersModel");
	}

	// auctions won
	public function won() {
		
		$this->checkLoggedIn();

		//estabilish userID
		$userID = abs(intval($this->loggedIn));

		// get the auctions that ended and this users had the latest bid on
		$auctionsWon = $this->db->query( 'SELECT * FROM listings 
		                                WHERE winnerID = ?', [ $userID ] );

		$msg = '';

		if( !$auctionsWon->num_rows() ) {
			$msg = 'No auctions on your win list';
		}

		$this->load->view('user-won', compact( 'auctionsWon','msg' ));

	}

	/*
		     * Messages/Read body
	*/
	public function read_message() {
		$this->checkLoggedIn();

		//estabilish fromID
		$userID = abs(intval($this->loggedIn));

		//estabilish msgID
		$msgID = $this->uri->segment(3);
		$msgID = abs(intval($msgID));

		if (!$msgID) {
			die("No msgID");
		}

		//get msg body
		$this->db->select("body")->from('messages')->where("msgID", $msgID)->where("toID", $userID);
		$rs = $this->db->get();

		if (count($rs)) {
			echo nl2br($rs->row()->body);
		} else {
			echo _('There is no message with this ID or you dont have the rights to read it');
		}

	}

	/*
		     * Messages/Send
	*/
	public function message() {
		$this->checkLoggedIn();

		//estabilish fromID
		$userID = abs(intval($this->loggedIn));

		//estabilish toID
		$toID = $this->uri->segment(3);
		$toID = abs(intval($toID));

		// set recipient username
		$recUsername = $this->db->select("username")
			->from("users")
			->where("userID", $toID)
			->get()
			->row();

		$data['recUsername'] = $recUsername->username;

		//check if in reply to
		if ($this->uri->segment(4) AND ($this->uri->segment(4) == 'replyto') AND $this->uri->segment(5)) {
			$replyTo = abs(intval($this->uri->segment(5)));
			if (!$replyTo) {
				die("Invalid replyto");
			}

			$this->db->select("subject");
			$this->db->from("messages");
			$this->db->where("msgID", $replyTo);
			$rs = $this->db->get()->row();

			if ($rs) {
				$data['reply_subject'] = _('Re : ') . $rs->subject;
			}

		}

		if (!$toID) {
			die(_('You received this page in error. Go Back!'));
		}

		if ($userID == $toID) {
			die(_('You cannot send a message to yourself!'));
		}

		if ($this->input->post('sb_msg')) {

			$subject = trim(strip_tags($this->input->post('subject')));
			$body = trim(strip_tags($this->input->post('body')));

			if (strlen($subject) < 5 || strlen($body) < 10) {
				$data['form_message'] = "<div class='alert alert-danger'>";
				$data['form_message'] .= _('Subject min 5 characters and body min 10 please.');
				$data['form_message'] .= '</div>';
			} else {

				$insert = [];
				$insert['fromID'] = $userID;
				$insert['toID'] = $toID;
				$insert['subject'] = $subject;
				$insert['body'] = $body;
				$insert['msg_date'] = time();

				$this->db->insert("messages", $insert);

				// add notification
				$noty = [ 'nUser' => $toID,
						  'nText' => 'Hey, you\'ve got a new message. <a href="/users/inbox">Inbox</a>' ];
				$this->db->insert( 'notifications', $noty );

				// get recipient
				$query = $this->db->query("SELECT username, email FROM users WHERE userID = ? LIMIT 1", [$toID]);
				$user_data = $query->row();

				// get recipient email
				$to = $user_data->email;

				// compute message
				$body = 'Hi there <srong>' . $user_data->username . '</strong>,<br/><br/>';

				$body .= 'You have received a new message:<br/>';
				$body .= '<br/>Please login to view the message!';
				$body .= '<br/><a href="' . base_url() . '?login=yes">' . base_url() . '?login=yes</a><br /><br />';
				$body .= 'Then go to your messages inbox<br/><br/>';
				$body .= '<a href="' . base_url() . 'users/inbox">' . base_url() . 'users/inbox</a><br /><br />';

				// get mail type
				$mail_type = get_option( 'mail_type', 'mail' );

				if( 'mail' == $mail_type ) {

					// email the listing owner
					$headers = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

					mail($to, "New Message Received", $body, $headers);

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

					$this->email->subject('New Message Received');
					$this->email->message($body);

					$this->email->send();

				}

				$data['form_message'] = "<div class='alert alert-success'>";
				$data['form_message'] .= _('Your message has been sent to the recipient.');
				$data['form_message'] .= '</div>';

			}
		}

		if (!isset($data)) {
			$data = [];
		}

		$data[ 'seo_title' ] = 'Send a Message';

		$this->load->view('user-msg', $data);

	}

	/*
		     * Messages/Inbox
	*/
	public function inbox() {
		$this->checkLoggedIn();

		//estabilish userID
		$userID = abs(intval($this->loggedIn));

		//get messages for this user
		$this->db->select("messages.*, username");
		$this->db->from("messages");
		$this->db->where(["toID" => $userID]);
		$this->db->join("users", "messages.fromID=users.userID");
		$this->db->order_by("msgID", "DESC");
		$messages = $this->db->get();

		$data['messages'] = $messages->result();

		if (!$messages->num_rows()) {
			$data['msg'] = _('You have no messages');
		}

		$data[ 'seo_title' ] = 'My Inbox';

		$this->load->view('user-inbox', $data);

	}

	/*
		     * Bids made
	*/
	public function offers() {
		$this->checkLoggedIn();

		//estabilish userID
		$userID = abs(intval($this->loggedIn));

		//if sold
		if ($this->uri->segment(3) AND ($this->uri->segment(3) == 'sold') AND $this->uri->segment(4)) {
			$listingID = abs(intval($this->uri->segment(4)));

			$lastBid = $this->db->select("MAX(amount) as amt")->from("bids")->where("bid_listing", $listingID);
			$lastBid = $this->db->get()->row()->amt;

			if ($this->db->update('listings',
				["sold" => 'Y', 'sold_date' => time(), "sold_price" => $lastBid],
				["listingID" => $listingID, "list_uID" => $userID])
			) {
				echo '<meta http-equiv="refresh" content="0;url=/users/offers">';
				exit;
			}
		}

		//if rejected
		if ($this->uri->segment(3) AND ($this->uri->segment(3) == 'reject') AND $this->uri->segment(4)) {
			$listingID = abs(intval($this->uri->segment(4)));
			if ($this->db->delete('bids', ["bidID" => $listingID, "owner_ID" => $userID])) {
				echo '<meta http-equiv="refresh" content="0;url=/users/offers">';
				exit;
			}
		}

		//get bids
		$bids = $this->db->query("SELECT bidID,listingID, listing_title,bid_date, username,
                                    amount, sold, sold_date FROM bids
                                    JOIN listings ON listingID = bid_listing
                                    JOIN users ON bidder_ID = userID
                                    WHERE listingID IN (SELECT CONCAT_WS(',', listingID) FROM listings WHERE list_uID = $userID)
                                    ORDER BY bidID DESC");
		if ($bids->num_rows()) {
			$bids = $bids->result();
			$data['bids'] = $bids;
		} else {
			$data['msg'] = _('No offers yet');
		}

		$data[ 'seo_title' ] = 'My Offers';

		$this->load->view('user-offers.php', $data);

	}

	/*
		     * Bids made
	*/
	public function bids() {
		$this->checkLoggedIn();

		//estabilish userID
		$userID = abs(intval($this->loggedIn));

		//get bids
		$bids = $this->db->query("SELECT bidID,listingID, listing_title, bid_date, username,
                                    amount, sold, sold_date, bid_type 
                                    FROM bids
                                    JOIN listings ON listingID = bid_listing
                                    JOIN users ON list_uID = userID
                                    WHERE bidder_ID = $userID
                                    ORDER BY bidID DESC");
		if ($bids->num_rows()) {
			$bids = $bids->result();
			$data['bids'] = $bids;
		} else {
			$data['msg'] = _('No bids made');
		}

		$data[ 'seo_title' ] = 'My Bids';

		$this->load->view('user-bids.php', $data);

	}

	/*
		     * User Listings
	*/
	public function mylistings() {
		$this->checkLoggedIn();

		$this->load->library('table');

		$userID = $this->loggedIn;

		$this->db->select("listingID, listing_title, FORMAT(bin,0 ) AS bin,
                           FROM_UNIXTIME(list_date, '%D %b %Y') as list_date,

                           CASE list_expires
                                WHEN 0 THEN '-'
                                ELSE
                                FROM_UNIXTIME(list_expires, '%D %b %Y')
                           END
                           AS list_expires,
                           sold,

                           CASE sold_date
                           WHEN 0
                               THEN '-'
                                    ELSE
                               FROM_UNIXTIME(sold_date, '%D %b %Y')
                           END
                           AS sold_date,

                           CASE
                           WHEN list_expires < '" . time() . "'
                                THEN CONCAT('<a href=\"/payments/relist/', listingID, '\" class=\"btn btn-xs btn-warning\">%s</a>')
                                ELSE '-'
                           END
                           AS payLink,

                           CONCAT('<a href=\"/users/goedit/', listingID, '\" class=\"btn btn-xs btn-default\">%s</a>') as editl, featured", false);

		$userListings = $this->db->get_where("listings", ["list_uID" => $userID]);

		$tmpl = ['table_open' => '<table class="table table-bordered table-hover">'];

		$this->table->set_template($tmpl);
		$this->table->set_heading('#ID', 'URL', 'Price', 'Date', 'Expires', 'Sold', 'Sold Date', 'Relist', '<b class="icon-edit"></b>');
		$data['table'] = $this->table->generate($userListings);

		$data['listings'] = $userListings->result();

		$data['listings_count'] = $userListings->num_rows();

		$data[ 'seo_title' ] = 'My Listings';

		$this->load->view('mylistings', $data);

	}

	/*
		     * Redirect to edit
	*/
	public function goedit() {
		ob_start();
		$this->checkLoggedIn();

		$id = $this->uri->segment(3);
		$id = abs(intval($id));

		if (!$id) {
			die("Edit #ID wrong");
		}

		//check if owner is correct
		$listing = $this->db->get_where("listings", ["listingID" => $id, "list_uID" => $this->loggedIn]);

		if (!$listing->num_rows()) {
			die(_("This listing isn't yours. Don't try edit other people listings"));
		} else {
			$this->session->set_userdata("listingID", $id);
			redirect('/users/updatelisting');
		}
		ob_end_flush();
	}

	/*
		     * User home
	*/
	public function index() {
		$this->checkLoggedIn();

		if ($this->input->post('sb_signup')) {
			if (!$this->input->post('email') OR !$this->input->post('password')) {
				$data['form_message'] = div_class("Email and password are required", 'alert alert-danger');
			} else {

				$this->db->where(["email" => $this->input->post('email', true)]);
				$this->db->where("userID != " . is_user_logged_in());
				$user = $this->db->get("users");

				if (count($user->result())) {
					$data['form_message'] = '<div class="alert alert-warning">';
					$data['form_message'] .= _('Username/Email taken, please chose another one.');
					$data['form_message'] .= '</div>';
				} else {

					//profile pic
					if (isset($_FILES['file']) AND $_FILES['file']['error'] == 0) {
						//make thumbnail
						$rand = md5(uniqid());
						$ext = explode(".", $_FILES['file']['name']);
						$ext = strtolower(end($ext));

						if (!@getimagesize($_FILES['file']['tmp_name'])) {
							die(_("Invalid picture"));
						}

						$config['image_library'] = 'gd2';
						#$config['source_image'] = getcwd() .'/uploads/' .  $rand . '.' . $ext;
						$config['source_image'] = $_FILES['file']['tmp_name'];
						$config['create_thumb'] = false;
						$config['maintain_ratio'] = true;
						$config['width'] = 48;
						$config['height'] = 48;
						$config['new_image'] = getcwd() . '/uploads/' . $rand . '.' . $ext;

						$this->load->library('image_lib', $config);

						$this->image_lib->resize();

						if (!$this->image_lib->resize()) {
							echo $this->image_lib->display_errors();
						} else {
							$thephoto = $rand . '.' . $ext;
							$this->db->where("userID", is_user_logged_in());
							$this->db->update("users", ['photo' => $thephoto]);
						}
					}

					$this->db->where("userID", is_user_logged_in());
					$this->db->update("users", ['email' => $this->input->post('email'),
						'password' => md5($this->input->post('password')),
						'about' => trim(strip_tags($this->input->post('about')))]);
					$data['form_message'] = div_class("Account updated", 'alert alert-success');

				}
			}
		}

		$user = $this->db->get_where("users", ["userID" => is_user_logged_in()]);
		$user = $user->row();
		$data['user'] = $user;

		$data[ 'seo_title' ] = 'My Account';

		$this->load->view('user-account', $data);
	}

	/*
		     * User Login
	*/
	public function login() {
		ob_start();

		if ($this->loggedIn) {
			redirect('/users');
			exit;
		}

		if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) OR $_SERVER['HTTP_X_REQUESTED_WITH'] != "XMLHttpRequest") {
			redirect("/?login=yes");
		}

		$data = [];

		$ref = isset( $_SERVER[ 'HTTP_REFERER' ] ) ? $_SERVER[ 'HTTP_REFERER' ] : '/users';

		if ($this->input->post('sbLogin')) {
			$user = $this->input->post('uname', true);
			$pass = $this->input->post('upwd', true);

			if (!empty($user) AND !empty($pass)) {
				$this->db->where(["username" => $user]);
				$this->db->where(["password" => md5($pass)]);
				$user = $this->db->get("users");

				if (is_object($user)) {
					echo '<div class="alert alert-success">Ok, redirecting..</div>';
					foreach ($user->result() as $u) {
						$this->session->set_userdata('loggedIn', $u->userID);
					}
					//echo '<meta http-equiv="refresh" content="1; url= /users" />';
					echo '<script>window.location.href = "'.$ref.'"</script>';
				} else {
					echo '<div class="alert alert-danger">' . _('Invalid username and/or password') . '</div>';
				}

			} else {
				echo '<div class="alert alert-danger">' . _('Invalid username and password') . '</div>';
			}

		}

	}

	public function loginForm() {

		$data = [];

		$ref = isset( $_SERVER[ 'HTTP_REFERER' ] ) ? $_SERVER[ 'HTTP_REFERER' ] : '/users';

		if ($this->input->post('sbLogin')) {
			$user = $this->input->post('uname', true);
			$pass = $this->input->post('upwd', true);

			if (!empty($user) AND !empty($pass)) {
				$this->db->where(["username" => $user]);
				$this->db->where(["password" => md5($pass)]);
				$user = $this->db->get("users");

				if (is_object($user)) {
					// echo '<div class="alert alert-success">Ok, redirecting..</div>';
					foreach ($user->result() as $u) {
						$this->session->set_userdata('loggedIn', $u->userID);
					}
					echo '<script>window.location.href = "'.$ref.'"</script>';
				} else {
					$this->session->set_userdata( 'login_message', '<div class="alert alert-danger">' . _('Invalid username and/or password') . '</div>');
					echo '<script>window.location.href = "'.$ref.'"</script>';
				}

			} else {
				$this->session->set_userdata( 'login_message', '<div class="alert alert-danger">' . _('Invalid username and password') . '</div>');
				echo '<script>window.location.href = "'.$ref.'"</script>';
			}

		}

	}

	/*
		     * Logout function
	*/
	public function logout() {
		$this->session->unset_userdata('loggedIn');
		redirect('/users/login');
	}

	/*
		     * Register Form/Page
	*/
	public function join() {
		if ($this->loggedIn) {
			redirect('/users');
			exit;
		}

		$this->load->view('join-now');
	}

	/*
		     * Register via AJAX
	*/
	public function ajax_join() {

		if ($this->input->post('sb_signup')) {

			unset($_POST['sb_signup']);

			$insert = [];

			foreach ($this->input->post() as $k => $v) {
				if ($this->input->post($k, true) != "") {
					$insert[$k] = $this->input->post($k, true);
				} else {
					print '<div class="alert alert-danger">';
					print _('All fields are mandatory');
					print '</div>';
					exit;
				}
			}

			$this->db->where(["username" => $this->input->post('username', true)]);
			$this->db->or_where(["email" => $this->input->post('email', true)]);
			$user = $this->db->get("users");

			if (is_object($user->result())) {
				print '<div class="alert alert-danger">';
				print _('Username/Email taken, please chose another one.');
				print '</div>';
				exit;
			}

			$insert['ip'] = ip2long($_SERVER['REMOTE_ADDR']);
			$insert['password'] = md5($insert['password']);

			if ($this->db->insert("users", $insert)) {
				$this->session->set_userdata('loggedIn', $this->db->insert_id());
				print '<div class="alert alert-success">';
				print _('You are now logged in. <a href="/users">My Account</a>');
				print '</div>';
			} else {
				print '<div class="alert alert-danger">';
				print _('DB Error');
				print '</div>';
			}

		} else {
			print '<div class="alert alert-danger">';
			print _('-No post-');
			print '</div>';
		}

	}

	/*
		     * User Profiles
	*/
	public function profile() {
		$username = trim(strip_tags($this->uri->segment(3)));

		if (!$username) {
			$data['error'] = _('User not found');
			$this->load->view('user-profiles', $data);
		} else {
			$user = $this->db->get_where("users", ["username" => $username]);
			$user = $user->row();
			$data['user'] = $user;

			if (is_object($user)) {
				//get listings
				$this->db->select("listingID, listing_title, bin, `starting_`, list_date,list_expires", false);
				$this->db->from("listings");
				$this->db->where("list_uID = $user->userID");
				$playlist = $this->db->get();
				$data['listings'] = $playlist->result();
				$data['tl'] = $playlist->num_rows();

				//get total bids
				$this->db->select("COUNT(*) as bids")->from("bids")->where("bidder_ID", $user->userID);
				$b = $this->db->get()->row();
				$data['tbids'] = $b->bids;
			} else {
				$data['listings'] = new stdClass;
			}

			$data[ 'seo_title' ] = ucfirst(strtolower($username)) . '\'s Profile';
			$this->load->view('user-profiles', $data);

		}

	}

	/*
		     * Add new listing
	*/
	public function newlisting() {
		$this->checkLoggedIn();

		$data['cats'] = get_categories();
		$data[ 'seo_title' ] = 'Start An Auction';
		$this->load->view('newlisting', $data);

	}

	/*
		     * Process new listing form
	*/
	public function addlisting() {
		$this->checkLoggedIn();

		// validate fields
		try {
			if (!isset($_POST['auction_title']) OR empty($_POST['auction_title'])) {
				throw new Exception("Auction title is required");
			}

			if (!isset($_POST['auction_description']) OR empty($_POST['auction_description'])) {
				throw new Exception("Auction description is required");
			}

			
			if (!isset($_POST['category'])) {
				throw new Exception("Auction category is required");
			}

			if (!isset($_POST['starting'], $_POST['bin'], $_POST['reserve'])) {
				throw new Exception("Starting, Reserve and BIN are required");
			}

			if (empty($_POST['starting']) OR empty($_POST['bin']) OR empty($_POST['reserve'])) {
				throw new Exception("Starting, Reserve and BIN cannot be left empty");
			}

			if (!isset($_FILES['p']) OR count($_FILES['p']['name']) < 3) {
				throw new Exception("Minimum 3 photos required to start an auction that will gain attraction!");
			}

			$starting = $this->input->post('starting', 1);
			$reserve = $this->input->post('reserve', 1);
			$bin = $this->input->post('bin', 1);

			$starting = preg_replace('/[^\d+]/', '', $starting);
			$reserve = preg_replace('/[^\d+]/', '', $reserve);
			$bin = preg_replace('/[^\d+]/', '', $bin);

			if( $reserve > $bin ) {
				throw new Exception("Reserve cannot be higher than BIN.", 1);
				
			}


			// build insert data
			$insert = [];
			$insert['list_catID'] = (int) $this->input->post('category', 1);
			$insert['listing_title'] = $this->input->post('auction_title', 1);
			$insert['starting_'] = (int) $starting;
			$insert['reserve'] = (int) $reserve;
			$insert['bin'] = (int) $bin;
			$insert['listing_description'] = $this->input->post('auction_description', 1);
			$insert['listing_status'] = (0 == get_option('listing_fee')) ? 'active' : 'inactive';
			$insert['featured'] = 'N';
			$insert['list_date'] = time();
			$insert['list_expires'] = strtotime("+" . get_option("listing_duration"));
			$insert['list_uID'] = $this->session->userdata('loggedIn');
			$insert['sold'] = 'N';

			// insert listing
			$this->db->insert('listings', $insert);

			// get id
			$id = $this->db->insert_id();

			// get listing info
			$l = $this->db->get_where("listings", ['listingID' => $id])->row();

			// process image uploads
			$images_count = count($_FILES['p']['name']);
			$p = $_FILES['p'];

			// load image library once
			$this->load->library('image_lib');

			for ($i = 0; $i < $images_count; $i++) {

				// get extension
				$ext = explode(".", $p['name'][$i]);
				$ext = strtolower(end($ext));
				$rand = md5(uniqid());

				if ($ext != "png" and $ext != "jpg" and $ext != "jpeg") {
					throw new Exception("File must be PNG/JPEG ONLY");
				}

				if (!@getimagesize($p['tmp_name'][$i])) {
					throw new Exception("Invalid/Corrupt image file. Try another one");
				}

				// echo 'Uploading image: '.$p['name'][$i].' => ' . $rand . '.' . $ext . '<br>';
				move_uploaded_file($p['tmp_name'][$i], getcwd() . '/uploads/' . $rand . '.' . $ext);

				//make thumbnail
				$config['image_library'] = 'gd2';
				$config['source_image'] = getcwd() . '/uploads/' . $rand . '.' . $ext;
				$config['create_thumb'] = false;
				$config['maintain_ratio'] = true;
				$config['width'] = 247;
				$config['height'] = 247;
				$config['new_image'] = getcwd() . '/uploads/small-' . $rand . '.' . $ext;

				// init image library
				$this->image_lib->clear();
				$this->image_lib->initialize($config);
				$this->image_lib->resize();

				if (!$this->image_lib->resize()) {
					throw new Exception("Small Image resizing error: " . $this->image_lib->display_errors());
				}

				// insert attachment into database
				$this->db->insert("attachments", ["listID" => $id, "att_file" => $rand . '.' . $ext]);

			} // foreach file loop

			if (0 == get_option('listing_fee')) {
				echo 'Congratulations, you auction is now live. <a href="' . auction_slug($l) . '">View it!</a>';
			} else {
				echo '<script>document.location.href="/select-payment-method";</script>';
			}

		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	/*
		     * AJAX Attachments
	*/
	public function att() {
		$this->checkLoggedIn();

		$id = $this->session->userdata("listingID");
		$id = abs(intval($id));
		$userID = $this->loggedIn;

		if (!$id) {
			die("Listing ID Not set");
		}

		if (!$this->input->post("sb_att")) {
			die("Page reached in error");
		}

		$att_title = $this->input->post('att_title');

		if (!$att_title or empty($att_title)) {
			die(_("Attachment title please"));
		}

		//image upload
		if (isset($_FILES['file'])) {

			//get extension
			$ext = explode(".", $_FILES['file']['name']);
			$ext = strtolower(end($ext));
			$rand = md5(uniqid());

			if ($ext != "png" and $ext != "jpg" and $ext != "jpeg") {
				echo '<div class="alert alert-danger">' . _("File must be PNG/JPEG ONLY") . '</div>';
				exit;
			}

			if (!@getimagesize($_FILES['file']['tmp_name'])) {
				echo '<div class="alert alert-danger">' . _("Invalid/Corrupt image file. Try another one") . '</div>';
				exit;
			}

			if (move_uploaded_file($_FILES['file']['tmp_name'], getcwd() . '/uploads/' . $rand . '.' . $ext)) {

				//make thumbnail
				$config['image_library'] = 'gd2';
				$config['source_image'] = getcwd() . '/uploads/' . $rand . '.' . $ext;
				$config['create_thumb'] = false;
				$config['maintain_ratio'] = true;
				$config['width'] = 44;
				$config['height'] = 26;
				$config['new_image'] = getcwd() . '/uploads/small-' . $rand . '.' . $ext;

				$this->load->library('image_lib', $config);

				$this->image_lib->resize();

				if (!$this->image_lib->resize()) {
					echo $this->image_lib->display_errors();
					exit;
				}

				$this->db->insert("attachments",
					["listID" => $id,
						"att_title" => trim(strip_tags($att_title)),
						"att_file" => $rand . '.' . $ext]);

				if ($this->db->affected_rows()) {
					echo '<script>window.parent.location.reload();</script>';
				} else {
					echo $this->db->last_error();
				}

			} else {
				echo _('Image could not be uploaded.');
			}

		} else {
			echo _("Please choose a file to be uploaded!");
		}
	}

	/*
		     * Remove attachments
	*/
	public function remove_att() {
		ob_start();

		$this->checkLoggedIn();

		$attID = $this->uri->segment(3);
		$attID = abs(intval($attID));
		$userID = $this->loggedIn;

		if (!$attID || !$userID) {
			exit(div_class('Error! No Attachment ID / UserID'));
		}

		//check if owns this attachments
		$rs = $this->db->get_where("attachments", ["attachID" => $attID]);
		$rs = $rs->row();

		if (!count($rs)) {
			die("No att with this id");
		}

		$rs = $this->db->query("select list_uID from listings where listingID = '$rs->listID'");
		$u = $rs->row();

		if (!count($u)) {
			die("could not get list owner info");
		}

		if ($u->list_uID != $userID) {
			die("You dont own this listing");
		}

		$this->db->delete("attachments", ["attachID" => $attID]);

		header("Location: /users/updatelisting");

		ob_end_flush();
	}

	/**
	 * @param $query
	 * @return string
	 */
	public function getGoogleAjaxAddress($query) {
		$key = get_option('googleApiKey');
		$placesRequest = urlencode($query);
		$urlRequest = 'https://maps.googleapis.com/maps/api/place/textsearch/json?query=' . $placesRequest . '&key=' . $key;

		$result = file_get_contents($urlRequest);
		$json = json_decode($result);

		if (isset($json->results) AND count($json->results)) {
			return json_encode($json->results);
		}

		return json_encode([]);

	}

	public function checkLoggedIn() {
		if (!$this->loggedIn) {
			redirect('/users/login');
			exit;
		}
	}

	public function balance() {

		$this->checkLoggedIn();

		$userID = $this->loggedIn;

		// save this user payout email
		if ($this->input->post('payment_email')) {
			$this->db->update('users', [
				'payout_email' => $this->input->post('payment_email', true),
			], ['userID' => $userID]);
			$data['msg'] = div_class('Successfully updated payment email!', 'alert alert-success');
		}

		// get this user transactions
		$tx = $this->db->select("txDate, amount, txStatus, listingID,
		                        listing_title, userID, email, username")
			->from('transactions')
			->where('sellerID', $userID)
			->join('listings', 'auctionID = listingID')
			->join('users', 'buyerID = userID')
			->get()
			->result();

		$data['tx'] = $tx;

		$payout_email = $this->db->select('payout_email')
			->from('users')
			->where('userID', $userID)
			->get()
			->row();

		$data['payout_email'] = $payout_email->payout_email;

		$data[ 'seo_title' ] = 'My Balance';

		$this->load->view('user-my-balance.php', $data);

	}

	public function updatelisting() {

		$this->checkLoggedIn();

		$listingID = $this->session->userdata('listingID');
		$listingID = abs(intval($listingID));
		$userID = $this->loggedIn;

		if (!$listingID || !$userID) {
			exit(div_class('Error! No ID / UserID'));
		}

		// get listing info
		$l = $this->db->get_where('listings', ['list_uID' => $userID, 'listingID' => $listingID])->row();

		if (!$l) {
			return $this->load->view('404');
		}

		// update listing
		if( isset($_POST['sbSave']) ) {

			$starting = $this->input->post('starting', 1);
			$reserve = $this->input->post('reserve', 1);
			$bin = $this->input->post('bin', 1);

			$starting = preg_replace('/[^\d+]/', '', $starting);
			$reserve = preg_replace('/[^\d+]/', '', $reserve);
			$bin = preg_replace('/[^\d+]/', '', $bin);

			
			// build insert data
			$update                        = [];
			$update['list_catID']          = (int) $this->input->post('category', 1);
			$update['listing_title']       = $this->input->post('auction_title', 1);
			$update['starting_']           = ( int ) $starting;
			$update['reserve']             = (int) $reserve;
			$update['bin']                 = (int) $bin;
			$update['listing_description'] = $this->input->post('auction_description', 1);

			// update listing
			$this->db->update('listings', $update, [ 'listingID' => $listingID ]);

			// upload more images to this listing
			if( isset( $_FILES[ 'p' ] ) AND $_FILES[ 'p' ][ 'error' ][ 0 ] == 0) {

				$images_count = count($_FILES['p']['name']);
				$p = $_FILES['p'];

				// load image library once
				$this->load->library('image_lib');

				for ($i = 0; $i < $images_count; $i++) {

					// get extension
					$ext = explode(".", $p['name'][$i]);
					$ext = strtolower(end($ext));
					$rand = md5(uniqid());

					if ($ext != "png" and $ext != "jpg" and $ext != "jpeg") {
						throw new Exception("File must be PNG/JPEG ONLY");
					}

					if (!@getimagesize($p['tmp_name'][$i])) {
						throw new Exception("Invalid/Corrupt image file. Try another one");
					}

					// echo 'Uploading image: '.$p['name'][$i].' => ' . $rand . '.' . $ext . '<br>';
					move_uploaded_file($p['tmp_name'][$i], getcwd() . '/uploads/' . $rand . '.' . $ext);

					//make thumbnail
					$config['image_library'] = 'gd2';
					$config['source_image'] = getcwd() . '/uploads/' . $rand . '.' . $ext;
					$config['create_thumb'] = false;
					$config['maintain_ratio'] = true;
					$config['width'] = 247;
					$config['height'] = 247;
					$config['new_image'] = getcwd() . '/uploads/small-' . $rand . '.' . $ext;

					// init image library
					$this->image_lib->clear();
					$this->image_lib->initialize($config);
					$this->image_lib->resize();

					if (!$this->image_lib->resize()) {
						throw new Exception("Small Image resizing error: " . $this->image_lib->display_errors());
					}

					// insert attachment into database
					$this->db->insert("attachments", ["listID" => $listingID, "att_file" => $rand . '.' . $ext]);

					} // foreach file loop
				}// if isset $_FILES[ 'p' ]

			$data[ 'msg' ] = div_class( 'Successfully updated listing', 'alert alert-warning' );

			$l = $this->db->get_where('listings', ['list_uID' => $userID, 'listingID' => $listingID])->row();

		}

		//get attachments
		$attachments = $this->db->get_where("attachments", ["listID" => $listingID])->result();

		$data['l']    = $l;
		$data['att']  = $attachments;
		$data['cats'] = get_categories();
		$data[ 'seo_title' ] = 'Update Listing';

		// return view
		$this->load->view('edit-listing', $data);

	}

}