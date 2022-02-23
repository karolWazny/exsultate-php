<?php

class ExsultateRestObtainSong
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
            register_rest_route( 'exsultate/v1', '/song/(?P<id>\d+)', array(
                'methods' => 'GET',
                'callback' => array($this, 'rest_callback'),
            ) );
        } );
    }

    public function rest_callback( $request ){
        $args = [
            'song_id'=>$request['id']
        ];
        $data = $this->obtain_song($args['song_id']);

        if(is_null($data)){
            return new WP_Error( 'invalid_song_id', 'No song with id provided', array('status' => 404) );
        }

        $response = new WP_REST_Response( $data );

        $response->set_status( 201 );

        return $response;
    }

    public function obtain_song( $id ){
        $post = get_post( $id );
        if('songs' === $post->post_type){
            $post_content = json_decode($post->post_content, true);
            return $post_content['song'];
        }
        return null;
    }
}