<?php

class ExsultateCustomPostTypeSong
{
    private static $_instance = null;

    private static $_cpt_data = [
        'labels' => [
            'name' =>  'Songs' ,
            'singular_name' => 'Song'
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'songs'],
        'show_in_rest' => false,

    ];

    private function __construct(){
        add_action( 'init', array($this, 'create_post_type' ));
    }

    public function create_post_type(){
        register_post_type( 'songs', self::$_cpt_data);
    }

    public static function instance(){
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}