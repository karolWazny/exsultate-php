<?php

class ExsultateSongbookAjax
{
    private static $_instance = null; //phpcs:ignore

    public static function instance( $file = '', $version = '1.0.0' ) {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self( $file, $version );
        }

        return self::$_instance;
    } // End instance ()

    public function __construct( $file = '', $version = '1.0.0' ){
        $this->register_shortcode();
        $this->scripts();

        add_action( 'wp_ajax_nopriv_get_songs_list_data', array( $this, 'ajax_songs_list_callback' ) );
        add_action( 'wp_ajax_get_songs_list_data', array( $this, 'ajax_songs_list_callback' ) );
    }

    private function scripts(){
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts_callback'));
    }

    public function enqueue_scripts_callback(){
        wp_enqueue_script( 'cart-related', exsultate()->assets_url . 'js/cart.js', array( 'jquery' ), null, true );
        wp_localize_script( 'cart-related', 'settings', array(
            'ajaxurl'    => admin_url( 'admin-ajax.php' ),
            'resturl' => get_rest_url(),
            'songbook_rest_path' => 'exsultate/v1/songbook/',
            'send_label' => __( 'Send report', 'reportabug'),
            'action' => 'get_songs_list_data')
        );
    }

    private function register_shortcode(){
        add_shortcode('exsultate_songs_list', array($this, 'shortcode_callback'));
    }

    public function shortcode_callback(){
        $string = '<ul id="exsultate_songs_list" class="exsultate_songs_list">
</ul>
<div class="wp-block-buttons">
  <button id="clear-cart-butt" onclick="clear_cart()">
    Wyczyść listę
  </button>
  <button id="generate-songbook-butt" onclick="generate_songbook()">
    Generuj ulotkę
  </button>
</div>';
        return $string;
    }

    public function ajax_songs_list_callback(){
        $data = $_POST;
        $song_ids_str = $data['song_ids'];
        $song_ids = [];
        foreach ($song_ids_str as $item) {
            $song_ids[] = intval($item);
        }

        $songs = get_posts(array(
            'include' => $song_ids,
            'post_type' => 'songs'
        ));

        $response = [];
        foreach ($songs as $song_object) {
            $relevant_data = [
                'title' => $song_object->post_title,
                'id' => $song_object->ID,
                'url' => get_permalink($song_object->ID)
            ];
            $response[] = $relevant_data;
        }

        wp_send_json_success($response);
    }
}