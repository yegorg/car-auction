<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Home extends CI_Controller {

	public function index() {

		$this->dolic();

		$this->load->model('Listings');

		$data = array();

		// get latest listings
		$listings = $this->db->query("SELECT listingID, listing_title, list_date, list_expires,
                                      FORMAT(`bin`, 0) AS `bin`,

                                      (
                                      CASE
                                      	WHEN ( SELECT COUNT(*) FROM bids WHERE bid_listing = listingID )
                                      	THEN
                                      		(SELECT FORMAT(amount,0) FROM bids WHERE bid_listing = listingID ORDER BY bidID DESC LIMIT 1)
                                      	ELSE
                                      		FORMAT(`starting_`, 0)
                                      	END
                                      ) AS `starting`

                                      FROM (`listings`)
                                      WHERE `listing_status` = 'active'
                                      AND `sold` = 'N'
                                      AND `list_expires` > " . time() . "

                                      ORDER BY `listingID`
                                      DESC LIMIT 4");

		$data['listings'] = $listings->result();

		// get featured listings
		$featured_listings = $this->db->query("SELECT listingID, listing_title, list_date, list_expires,
                                      FORMAT(`bin`, 0) AS `bin`,

                                      (
                                      CASE
                                      	WHEN ( SELECT COUNT(*) FROM bids WHERE bid_listing = listingID )
                                      	THEN
                                      		(SELECT FORMAT(amount,0) FROM bids WHERE bid_listing = listingID ORDER BY bidID DESC LIMIT 1)
                                      	ELSE
                                      		FORMAT(`starting_`, 0)
                                      	END
                                      ) AS `starting`

                                      FROM (`listings`)
                                      WHERE `listing_status` = 'active'
                                      AND `sold` = 'N'
                                      AND `featured` = 'Y'
                                      AND `list_expires` > " . time() . "

                                      ORDER BY `listingID`
                                      DESC LIMIT 4");
		$data['featured_listings'] = $featured_listings->result();

		// pass categories to view
		$data['cats'] = get_categories();

		$this->load->view('home', $data);
	}

	public function tos() {
		$this->load->model("UsersModel");

		$tos = $this->db->get("tos", 1);
		$data['tos'] = $tos->row();
		$data['seo_title'] = 'Terms of Service  -  ' . get_option('seo_title');

		$this->load->view('tos', $data);
	}

	public function contact() {
		$this->load->model('UsersModel');

		$data = array();
		$data['seo_title'] = 'Contact  -  ' . get_option('seo_title');
		$this->load->view('contact', $data);
	}

	public function contactajax() {

		foreach ($_POST as $k => $v) {
			$_POST[$k] = trim(strip_tags($v));
			if (empty($_POST[$k])) {
				die('All fields are required');
			}

		}

		$body = 'From: ' . $_POST['yname'];
		$body .= "\r\n";

		$body .= 'Email: ' . $_POST['yemail'];
		$body .= "\r\n";

		$body .= 'Subject: ' . $_POST['ysubject'];
		$body .= "\r\n";

		$body .= 'Message: ' . nl2br(str_replace("<br>", "\n\r", $_POST['ymessage']));

		// mail type
		$mail_type = get_option( 'mail_type', 'mail' );

		if( 'mail' == $mail_type ) {
			mail(get_option('contact_email'), 'Contact Form', $body);
		}elseif( 'smtp' == $mail_type) {

			// var_dump('SMTP');

			// get SMTP mail server details
			$mail_server = get_option( 'smtp_address' );
			$mail_port = get_option( 'smtp_port' );
			$mail_user = get_option( 'smtp_user' );
			$mail_pass = get_option( 'smtp_password' );
			$contact_email = get_option( 'contact_email' );
			$to = get_option('contact_email');

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
			$this->email->reply_to($_POST[ 'yemail' ]);

			$this->email->subject($_POST[ 'ysubject' ]);
			$this->email->message($body);

			$this->email->send();

		}

		echo '<div class="alert alert-success">Thanks for contacting us! We will get back to you soon.</div>';
		echo '<script>$("#contact-form").hide();</script>';

	}

	public function searchautocomplete() {

		$q = $this->uri->segment(3);
		if (!$q) {
			die();
		}

		$string = trim(strip_tags($q));
		$db_string = urldecode($string);

		$this->db->select("listingID, listing_title, listing_url, listing_status");
		$this->db->like("listing_title", $db_string);
		$this->db->or_like("listing_url", $db_string);

		$listings = $this->db->get('listings', 10);

		if (!count($listings->result())) {
			die('No results');
		}

		?>

		<ul class="playlist">
		<?php
foreach ($listings->result() as $m):
			if ($m->listing_status != 'active' OR empty($m->listing_title)) {
				continue;
			}

			?>
																				<li>
																					<hr>

																					<a href="<?php echo '/listings/' . $m->listingID . '/' . url_title($m->listing_title); ?>" class="url-listing-title" style="font-size:14px;">
																					<i class="icon icon-tag"></i> <?php echo $m->listing_url; ?>
																					</a>
																					<br />
																					<a href="<?php echo '/listings/' . $m->listingID . '/' . url_title($m->listing_title); ?>">
																					<small><?php echo $m->listing_title; ?></small>
																					</a>
																				</li>
																				<?php endforeach;?>
		<li>&nbsp;</li>
		</ul>

		<?php

	}

	public function lostpassword() {

		$data = array('msg' => '');

		if ($e = $this->input->post('ea')) {
			if (filter_var($e, FILTER_VALIDATE_EMAIL)) {

				// get this user details from db
				$query = $this->db->query("SELECT userID, username, email FROM users WHERE email = ? LIMIT 1", array($e));

				if ($query->num_rows() > 0) {
					$row = $query->row();

					$hash = md5($row->userID . $row->email);
					$to = $row->email;
					$subject = 'Password Reset Email';

					$body = 'Hi there <srong>' . $row->username . '</strong>,<br/><br/>';

					$body .= 'You have requested a password reset email:<br/>';
					$body .= '<a href="' . base_url() . 'home/resetpwd?hash=' . $hash . '">' . base_url() . '/home/resetpwd?hash=' . $hash . '</a>';

					$body .= '<br/><br/>Ignore if it wasn\'t you to request this password reset email!';

					// To send HTML mail, the Content-type header must be set
					$headers = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

					// get mail type
					$mail_type = get_option( 'mail_type', 'mail' );

					if( 'mail' == $mail_type ) {
						mail($to, $subject, $body, $headers);
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

						$this->email->subject($subject);
						$this->email->message($body);

						$this->email->send();

					}

					$data['msg'] = '<div class="alert alert-success">Please check your inbox/spambox for the reset link!</div>';

				} else {
					$data['msg'] = '<div class="alert alert-danger">No such email in database.</div>';
				}

			} else {
				$data['msg'] = '<div class="alert alert-danger">Invalid email.</div>';
			}
		}

		$this->load->view('lost-password', $data);

	}

	public function resetpwd() {

		if ($hash = $this->input->get('hash')) {

			$data['msg'] = '';

			$hash = trim(strip_tags($hash));

			// get this user details from db
			$query = $this->db->query("SELECT userID FROM users WHERE MD5(CONCAT(userID, email)) = ? LIMIT 1", array($hash));
			if ($query->num_rows() > 0) {
				$row = $query->row();

				if ($new_pwd = $this->input->post('pn')) {

					if (empty($new_pwd)) {
						die("No empty password allowed");
					}

					$reset = $this->db->query("UPDATE users SET password = MD5(?) WHERE userID = ?", array($new_pwd, $row->userID));
					if ($this->db->affected_rows()) {
						$data['msg'] = '<div class="alert alert-success">Successfully reset password. You may now login with the new credentials!</div>';
					}

				}

			} else {
				die("Invalid hash!");
			}

			$this->load->view('reset-password', $data);

		} else {
			$this->load->view('404');
		}

	}

	public function welcome(  ) {
		$this->load->view( 'welcome' );
	}

	public function dolic(  ) {
		
		if( get_option( 'license_valid' ) )
			return true;

		// load config
		$this->config->load('license');

		// load license key
		$l = $this->config->item('license_key');

		if( !$l or empty( $l ) or $l == 'ENTER_LICENSE_KEY' ) {
			echo '<h3>Please validate your product by adding your license key in <strong style="color:red">application/config/license.php</strong></h3>';
			exit;
		}

		$url = $this->config->item('base_url');


		// validate
		// call url for licensing
		$apiurl = 'http://crivion.com/envato-licensing/index.php';

		//open connection
		$ch = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $apiurl);
		curl_setopt($ch,CURLOPT_POST, 2);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS, 'product=PHP+Auctions&license_code=' . $l . '&blogURL=' . $url);
		curl_setopt($ch,CURLOPT_USERAGENT, 'crivion/envato-license-checker-v1.0');

		//execute post
		$result = curl_exec($ch);

		//close connection
		curl_close($ch);

		//if LICENSE_VALID_AUTOUPDATE_ENABLED
		if( $result == 'LICENSE_VALID_AUTOUPDATE_ENABLED' )  {
			set_option('license_valid', 'Yes');
		} else {
			echo $result;
			exit;
		}

	}

	public function upgrade(  ) {

		if( isset( $_GET[ 'confirm' ] ) AND $_GET[ 'confirm' ] == 'true' ) {

		
			$this->db->query( "alter table listings add winnerID INT NULL after sold_price, add isPaid ENUM('No','Yes') NOT NULL DEFAULT 'No' AFTER winnerID, add wonAmount INT NULL AFTER isPaid;" );
			
			echo '<h1>You have successfully upgraded the database.</h1>';
			echo '<a href="/">Home</a>';

			exit;

		}

		$this->load->view( 'upgrade' );

	}

}