<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Cron extends CI_Controller {

	// pick winners for expired auctions
	public function pickWinners() {

		// select expired auctions without winners
		$expireAuctionsWithoutWinner = $this->db->query( 'SELECT listingID, listing_title, list_uID,

		                                                  ( SELECT bidID FROM bids 
		                                                  	WHERE bid_listing = listingID 
		                                                  	ORDER BY bidID DESC LIMIT 1 ) AS winnerBidID

		                                                 FROM listings 
		                                                 WHERE 
		                                                 list_expires < ?
		                                                 AND ISNULL(winnerID)', [ time() ] );

		if ($expireAuctionsWithoutWinner->num_rows() > 0) {

		   foreach ($expireAuctionsWithoutWinner->result() as $l)
		   {

		   		// now get the latest bidder
		   		if( is_null( $l->winnerBidID ) )
		   			continue;

		   		// get bid and user details
		   		$bidInfo = $this->db->query( 'SELECT bids.*, users.username, users.email FROM bids 
		   		                            JOIN users ON users.userID = bids.bidder_ID
		   		                            WHERE bidID = ?', [ $l->winnerBidID ] );
		   		$bidInfo = $bidInfo->row();


		   		if( is_object( $bidInfo ) ) {


			   		// update winner id and amount in the listing
			   		$this->db->query( 'UPDATE listings 
			   		                 SET winnerID = ?, wonAmount = ? 
			   		                 WHERE listingID = ?', [$bidInfo->bidder_ID, $bidInfo->amount ,$l->listingID ]);

			   		// notify user by email
			   		$this->notifyWinnerViaEmail( $bidInfo->email, $bidInfo->username, $l );

			   		// notifiy owner by email
			   		$this->notifySellerViaEmail($l);

			   	}
		   		
		   }

		}else{
			echo 'No winners to pick';
		}


	}


	public function notifyWinnerViaEmail($toEmail, $toUsername, $auction) {

		// mail type
		$mail_type = get_option( 'mail_type', 'mail' );

		// set subject
		$subject = 'Good news: You\'ve won an auction!';

		// get site url
		$this->load->helper('url');
		$siteUrl = $this->config->base_url();

		// set body
		$body = 'Good news <strong>'.$toUsername.'</strong><br><br>
				You\'ve won an auction called: <strong>'.$auction->listing_title.'</strong><br><br>
				You can check the auction here: <a href="'.auction_slug($auction).'">'.auction_slug($auction).'</a><br><br>
				Login to your <strong>Account -> Auction Won</strong> to finish the checkout process and get your item.<br><br>
				Thank you for participating<br>
				<a href="'.$siteUrl.'">'.$siteUrl.'</a>';

		if( 'mail' == $mail_type ) {
			mail($toEmail, $subject, $body);
		}elseif( 'smtp' == $mail_type) {

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
			$this->email->to($toEmail);
			$this->email->reply_to($contact_email);

			$this->email->subject($subject);
			$this->email->message($body);

			$this->email->send();

		}
		
	}

	public function notifySellerViaEmail($auction) {

		// mail type
		$mail_type = get_option( 'mail_type', 'mail' );

		// set subject
		$subject = 'Good news: You\'ve sold on auction!';

		// get site url
		$this->load->helper('url');
		$siteUrl = $this->config->base_url();

		// set to Email
		$user = $this->db->query( "SELECT * FROM users WHERE userID = ?", [ $auction->list_uID ] );
		$user = $user->row();

		$toUsername = $user->username;
		$toEmail = $user->email;

		// set body
		$body = 'Good news <strong>'.$toUsername.'</strong><br><br>
				You\'ve won sold at an auction called: <strong>'.$auction->listing_title.'</strong><br><br>
				You can check the auction here: <a href="'.auction_slug($auction).'">'.auction_slug($auction).'</a><br><br>
				We have notified and are waiting the user to pay for the listing and we will let you know once it is complete.<br><br>
				Thank you for selling with us<br>
				<a href="'.$siteUrl.'">'.$siteUrl.'</a>';

		if( 'mail' == $mail_type ) {
			mail($toEmail, $subject, $body);
		}elseif( 'smtp' == $mail_type) {

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
			$this->email->to($toEmail);
			$this->email->reply_to($contact_email);

			$this->email->subject($subject);
			$this->email->message($body);

			$this->email->send();

		}
		
	}

}