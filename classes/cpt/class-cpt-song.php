<?php

require_once 'class-song-display-formatter.php';
require_once 'class-song-save-formatter.php';

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

        add_filter( 'wp_insert_post_data', array($this, 'insert_song_data'), 10, 2 );
        add_filter( 'default_content', array($this, 'default_content_callback'), 10, 2 );

        add_action( 'the_post', array($this, 'filter_song_data_for_edition'), 500 );
        add_filter( 'the_content', array($this, 'display_song_content_filter'), 5 );
        add_filter( 'the_content', array($this, 'polish_notation_callback'), 999 );
    }

    private function twelve_keys(){
        return [
            'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'B', 'H'
        ];
    }

    private function twelve_minor_keys(){
        $keys = $this->twelve_keys();
        $minor_keys = [];
        foreach ($keys as $key) {
            $minor_keys[] = $key . 'm';
        }
        return $minor_keys;
    }

    private function major_polish(){
        return [
            'C', 'Cis', 'D', 'Dis', 'E', 'F', 'Fis', 'G', 'Gis', 'A', 'B', 'H'
        ];
    }

    private function minor_polish() {
        $major = $this->major_polish();
        $output = [];
        foreach ($major as $item){
            $output[] = strtolower($item);
        }
        return $output;
    }

    public function polish_notation_callback( $content ) {
        $content = str_replace('Transpose up', 'Inna tonacja', $content);

        $minor_chords_english = $this->twelve_minor_keys();
        $minor_chords_polish = $this->minor_polish();

        $searches = [];
        foreach ($minor_chords_english as $item){
            $searches[] = '<span class="chordshort">' . $item;
        }

        $replacements = [];
        foreach ($minor_chords_polish as $item){
            $replacements[] = '<span class="chordshort">' . $item;
        }

        $content = str_replace($searches, $replacements, $content);

        $major_chords_english = $this->twelve_keys();
        $major_chords_polish = $this->major_polish();

        $searches = [];
        foreach ($major_chords_english as $item){
            $searches[] = '<span class="chordshort">' . $item;
        }

        $replacements = [];
        foreach ($major_chords_polish as $item){
            $replacements[] = '<span class="chordshort">' . $item;
        }

        return str_replace($searches, $replacements, $content);
    }

    public function display_song_content_filter( $content ){
        global $post;

        //that line prevents from formatting
        //the same post twice;
        //this was an issue and I don't know WTF
        if(isset($_GET['action']) && $_GET['action'] == 'edit') {
            return $content;
        }

        if ($post->post_type == 'songs') {
            $formatter = new ExsultateSongDisplayFormatter();

            return $formatter->format( $content, $post->post_id);
        }
        return $content;
    }

    public function default_content_callback( $content, $post ) {
        if ( $post->post_type == 'songs' ) {
            return self::$_default_content;
        }
        return $content;
    }

    public function filter_song_data_for_edition( $post ){
        if( $post->post_type == 'songs' ) {
            if(isset($_GET['action']) && $_GET['action'] == 'edit') {
                $formatter = new ExsultateSongDisplayFormatter();
                $formatter->format( $post->post_content, $post->post_id);
            }
        }
    }

    public function insert_song_data($data, $postarr){

        $formatter = new ExsultateSongSaveFormatter();
        return $formatter->format( $data );
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