<?php

require_once 'class-obtain-song.php';
require_once 'class-obtain-songbook.php';

class ExsultateRest
{
    private static $_instance = null; //phpcs:ignore

    private $song_json_endpoint = null;
    private $songbook_json_endpoint;

    public static function instance( $file = '', $version = '1.0.0' ) {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self( $file, $version );
        }
        return self::$_instance;
    } // End instance ()

    public function __construct( $file = '', $version = '1.0.0' ){
        $this->song_json_endpoint = ExsultateRestObtainSong::instance();
        $this->songbook_json_endpoint = ExsultateRestObtainSongbook::instance();

        $this->register_endpoints();
    }

    public function my_awesome_func( $data ){
        $data = array( 'some', 'response', 'data' );

// Create the response object
        $response = new WP_REST_Response( $data );

// Add a custom status code
        $response->set_status( 201 );

// Add a custom header
        $response->header( 'Location', 'http://example.com/' );

        return $response;
    }

    private function register_endpoints(){
        add_action( 'rest_api_init', function () {
            register_rest_route( 'exsultate/v1', '/author/(?P<id>\d+)', array(
                'methods' => 'GET',
                'callback' => array($this, 'my_awesome_func'),
            ) );
        } );
    }
}