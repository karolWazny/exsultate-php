<?php

require_once 'class-obtain-songbook.php';

class ExsultateRestGenerateFormatedSongbook
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
            register_rest_route( 'exsultate/v1', '/songbook/(?P<ids>[0-9+]+)', array(
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

        $obtainer = ExsultateRestObtainSongbook::instance();

        $data = $obtainer->obtain_songs($song_ids);

        if(empty($data)){
            return new WP_Error( 'invalid_song_id', 'No songs with ids provided', array('status' => 404) );
        }

        $generator_input = $this->wrap_generator_input( $data );

        $command = 'python3 ' . plugin_dir_path(__FILE__) . 'python/main.py \'' . json_encode($generator_input) . '\' dupoa';

        $python_output = shell_exec($command);

        if(is_null($python_output)){
            return new WP_Error( 'dupa', 'Something went wrong', array('status' => 203) );
        }

        //here I'm gonna need some validation and error checking
        //but ain't done it yet
        $filename = plugin_dir_path(__FILE__) . 'python/' . trim($python_output);
        $this->download_file( $filename, 'ulotka.docx');

        $response = new WP_REST_Response( $data );

        $response->set_status( 201 );

        return $response;
    }

    private function extract_ids_from_request( $request ){
        $ids_string = $request['ids'];
        $id_strings = explode('+', $ids_string);
        $song_ids = [];
        foreach ($id_strings as $single_id_string){
            $song_ids[] = intval($single_id_string);
        }
    }

    private function wrap_generator_input( $data ){
        return [
            'songs' => $data
        ];
    }

    public function download_file( $filename, $as){
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . basename('ulotka.docx'));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        readfile($filename);

        //die;
        //die();
        //exit;
        exit();
    }
}