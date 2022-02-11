<?php

class ExsultateCustomPostTypeSong
{
    private static $_instance = null;
    //this is just a string, but due to some strange stuff going on inside it,
    //it is enclosed in an unusual way
    private static $_default_content = <<<END
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"66.66%"} -->
<div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:lazyblock/song {"blockId":"18RaRQ","blockUniqueClass":"lazyblock-song-18RaRQ"} /--></div>
<!-- /wp:column -->

<!-- wp:column {"width":"33.33%"} -->
<div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:lazyblock/access-restriction {"blockId":"ZBljDs","blockUniqueClass":"lazyblock-access-restriction-ZBljDs"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
END;

    private function __construct(){
        add_action( 'init', array($this, 'create_post_type' ));

        //add_action ( 'save_post_songs', array($this, 'save_song_data' ), 10, 3 );
        add_filter ( 'wp_insert_post_data', array($this, 'insert_song_data'), 10, 2 );
        add_filter( 'default_content', array($this, 'default_content_callback'), 10, 2 );

        //add_action('load-post.php', array($this, 'admin_init'));
        //add_action('save_post', array($this, 'save_price'));
    }

    public function default_content_callback( $content, $post ) {
        if ( $post->post_type == 'songs' ) {
            return self::$_default_content;
        }
        return $content;
    }

    public function insert_song_data($data, $postarr){
        $myfile = fopen(plugin_dir_path( __FILE__ ) . "data.txt", "w");
        fwrite($myfile, $data['post_content']);
        fclose($myfile);

        return $data;
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

    public function admin_init(){
        add_meta_box("someId", "Product Options", "meta_options");
    }

    public function meta_options(){
        global $post;
        $custom = get_post_custom($post->ID);
        $price = $custom["price"][0];
        ?>
        <label>Price:</label><input name="price" value="<?php echo $price; ?>" />
        <?php
    }

    public function save_price(){
        global $post;
        update_post_meta($post->ID, "price", $_POST["price"]);
    }
}