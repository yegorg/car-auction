<?php if ( !defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

if ( !function_exists( 'is_home' ) ) {

    function is_home ()
    {
        $CI =& get_instance();

        return ( !$CI->uri->segment( 1 ) ) ? true : false;
    }

}
