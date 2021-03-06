<?php

require_once 'class-obtain-song.php';

class ExsultateRestObtainSongbook
{
    private static $_instance = null; //phpcs:ignore

    public static function instance( $file = '', $version = '1.0.0' ) {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self( $file, $version );
        }

        return self::$_instance;
    } // End instance ()

    public function __construct( $file = '', $version = '1.0.0' ){
        $this->register_endpoint();
    }

    public function register_endpoint(){
        add_action( 'rest_api_init', function () {
            register_rest_route( 'exsultate/v1', '/songs/(?P<ids>[0-9+]+)', array(
                'methods' => 'GET',
                'callback' => array($this, 'rest_callback'),
            ) );
        } );
    }

    public function rest_callback( $request ){
        $ids_string = $request['ids'];
        $id_strings = explode('+', $ids_string);
        $song_ids = [];
        foreach ($id_strings as $single_id_string){
            $song_ids[] = intval($single_id_string);
        }

        $data = $this->obtain_songs($song_ids);

        if(empty($data)){
            return new WP_Error( 'invalid_song_id', 'No songs with ids provided', array('status' => 404) );
        }

        $response = new WP_REST_Response( $data );

        $response->set_status( 201 );

        return $response;
    }

    public function obtain_songs( $ids ){
        $data = [];

        foreach ($ids as $song_id){
            $obtainer = ExsultateRestObtainSong::instance();
            $song = $obtainer->obtain_song($song_id);
            if(! is_null($song)){
                $data[] = $song;
            }
        }

        return $data;
    }
}