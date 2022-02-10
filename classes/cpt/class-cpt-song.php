<?php

class ExsultateCustomPostTypeSong
{
    private static $_instance = null;

    private function __construct(){
        add_action( 'init', array($this, 'create_post_type' ));
    }

    public function create_post_type(){
        $labels = array(
            'name'                => _x( 'Songs', 'Post Type General Name', 'twentytwenty' ),
            'singular_name'       => _x( 'Song', 'Post Type Singular Name', 'twentytwenty' ),
            'menu_name'           => __( 'Songs', 'twentytwenty' ),
            'parent_item_colon'   => __( 'Parent Song', 'twentytwenty' ),
            'all_items'           => __( 'All Songs', 'twentytwenty' ),
            'view_item'           => __( 'View Song', 'twentytwenty' ),
            'add_new_item'        => __( 'Add New Song', 'twentytwenty' ),
            'add_new'             => __( 'Add New', 'twentytwenty' ),
            'edit_item'           => __( 'Edit Song', 'twentytwenty' ),
            'update_item'         => __( 'Update Song', 'twentytwenty' ),
            'search_items'        => __( 'Search Song', 'twentytwenty' ),
            'not_found'           => __( 'Not Found', 'twentytwenty' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'twentytwenty' ),
        );

// Set other options for Custom Post Type

        $args = array(
            'label'               => __( 'songs', 'twentytwenty' ),
            'description'         => __( 'Lyrics, chords and sheets.', 'twentytwenty' ),
            'labels'              => $labels,
            // Features this CPT supports in Post Editor
            'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'revisions', 'custom-fields' ),
            // You can associate this CPT with a taxonomy or custom taxonomy.
            'taxonomies'          => array( 'post_tag', 'category' ),
            /* A hierarchical CPT is like Pages and can have
            * Parent and child items. A non-hierarchical CPT
            * is like Posts.
            */
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'show_in_rest' => true,

        );

        unregister_post_type( 'songs' );

        // Registering your Custom Post Type
        register_post_type( 'songs', $args );

        //register_post_type( 'songs', self::$_cpt_data);
    }

    public static function instance(){
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}