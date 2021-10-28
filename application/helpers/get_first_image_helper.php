<?php if ( !defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

if ( !function_exists( 'get_first_image' ) ) {

    /**
     * @param $listingID
     * @param string $type (THUMB, FULL)
     * @return string
     */
    function get_first_image ( $listingID, $type = '' )
    {

        // get images for this listingID
        $CI       =& get_instance();
        $settings = $CI->db->get_where( "attachments", [ "listID" => $listingID ] );

        // return default file if no images found
        if ( !is_object( $settings->row() ) )
            return base_url() . 'uploads/default.jpg';

        // image info
        $image = $settings->row();

        // if type is thumbnail, append small-
        $type = $type == 'THUMB' ? 'small-' : '';

        // return full img src path
        return base_url() . '/uploads/' . $type . $image->att_file;

    }

}