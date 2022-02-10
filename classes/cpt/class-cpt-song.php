<?php

class ExsultateCustomPostTypeSong
{
    private static $_instance = null;

    private function __construct(){
        add_action( 'init', array($this, 'create_post_type' ));
    }

    public function create_post_type(){
        register_post_type( 'songs',
            // CPT Options
            array(
                'labels' => array(
                    'name' => __( 'Songs' ),
                    'singular_name' => __( 'Song' )
                ),
                'public' => true,
                'has_archive' => true,
                'rewrite' => array('slug' => 'songs'),
                'show_in_rest' => false,

            )
        );
    }

    public static function instance(){
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}