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
            'send_label' => __( 'Send report', 'reportabug'),
            'action' => 'get_songs_list_data')
        );
    }

    private function register_shortcode(){
        add_shortcode('exsultate_songs_list', array($this, 'shortcode_callback'));
    }

    public function shortcode_callback(){
        $string = '<ul id="exsultate_songs_list">
</ul>
<div class="wp-block-buttons">
  <button id="clear-cart-butt" onclick="clear_cart()">
    Wyczyść listę
  </button>
</div>';
        return $string;
    }

    public function ajax_songs_list_callback(){
        $data = $_POST;

        $response = $data['song_ids'];
        wp_send_json_success($response);
    }
}