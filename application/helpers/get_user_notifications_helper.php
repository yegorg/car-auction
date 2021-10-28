<?php if ( !defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

if ( !function_exists( 'get_user_notifications' ) ) {

    /**
     * @param $listingID
     * @param string $type (THUMB, FULL)
     * @return string
     */
    function get_user_notifications ()
    {

    	// set CI Instance
        $CI       =& get_instance();

        // get the logged in user
        $userID = $CI->session->userdata( 'loggedIn' );

        if( !$userID )
        	return false;

        // get this user new notifications
        $notifications = $CI->db->get_where( "notifications", [ "nUser" => $userID, "nStatus" => 'Unread' ] );

        // return default file if no images found
        if ( !is_object( $notifications->row() ) )
            return false;

        $return = '<script>$(document).ready(function() {';
        foreach( $notifications->result() as $n ) {
        	$text = addslashes($n->nText);
        	$return .= <<<EOF
        	swal({
  title: "News for you",
  text: "$text",
  type: "success",
  html: true
});
EOF;
        }
        $return .= '});</script>';

        // set all this user requests to read
        $CI->db->update( "notifications", [ 'nStatus' => 'Read' ], ["nUser" => $userID] );

        return $return;

    }

}