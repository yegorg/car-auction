<?php if ( !defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

    if ( !function_exists( 'auction_slug' ) ) {

        function auction_slug ( $listing )
        {


            $title = $listing->listing_title;
            $id    = $listing->listingID;

            $text = preg_replace( '~[^\pL\d]+~u', '-', $title );

            // transliterate
            $text = iconv( 'utf-8', 'us-ascii//TRANSLIT', $text );

            // remove unwanted characters
            $text = preg_replace( '~[^-\w]+~', '', $text );

            // trim
            $text = trim( $text, '-' );

            // remove duplicate -
            $text = preg_replace( '~-+~', '-', $text );

            // lowercase
            $text = base_url() . 'auctions/' . (int) $id .'-'. strtolower( $text );

            if ( empty( $text ) ) {
                return 'n-a';
            }

            return $text;
        }

    }
