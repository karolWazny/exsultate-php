<?php
require_once 'classes/rest/class-exsultate-rest.php';
require_once 'classes/cpt/class-cpt-song.php';
require_once 'classes/frontend/class-songbook-ajax.php';
class Exsultate
{
    private static $_instance = null; //phpcs:ignore

    /**
     * Local instance of WordPress_Plugin_Template_Admin_API
     *
     * @var WordPress_Plugin_Template_Admin_API|null
     */
    public $admin = null;

    /**
     * Settings class object
     *
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public $settings = null;

    public $_version; //phpcs:ignore

    public $_token; //phpcs:ignore

    public $file;

    public $dir;

    public $assets_dir;

    public $assets_url;

    public $script_suffix;

    private $rest;
    private $cpt;
    private $ajax;

    /**
     * Constructor funtion.
     *
     * @param string $file File constructor.
     * @param string $version Plugin version.
     */
    public function __construct( $file = '', $version = '1.0.0' ) {
        $this->rest = ExsultateRest::instance();
        $this->cpt = ExsultateCustomPostTypeSong::instance();
        $this->ajax = ExsultateSongbookAjax::instance();
        $this->_version = $version;
        $this->_token   = 'exsultate';

        // Load plugin environment variables.
        $this->file       = plugin_dir_path(__FILE__) . 'class-exsultate.php';
        $this->dir        = dirname( $this->file );
        $this->assets_dir = trailingslashit( $this->dir ) . 'assets';

        $this->assets_url = plugin_dir_url(__FILE__) . '/assets/';

        $this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

        register_activation_hook( $this->file, array( $this, 'activate') );
        register_deactivation_hook( $this->file, array( $this, 'deactivate' ) );

        // Load frontend JS & CSS.
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

        // Load admin JS & CSS.
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

        // Load API for generic admin functions.
//        if ( is_admin() ) {
//            $this->admin = new WordPress_Plugin_Template_Admin_API();
//        }

        // Handle localisation.
        $this->load_plugin_textdomain();
        add_action( 'init', array( $this, 'load_localisation' ), 0 );
    } // End __construct ()

    /**
     * Wrapper function to register a new taxonomy.
     *
     * @param string $taxonomy Taxonomy.
     * @param string $plural Plural Label.
     * @param string $single Single Label.
     * @param array  $post_types Post types to register this taxonomy for.
     * @param array  $taxonomy_args Taxonomy arguments.
     *
     * @return bool|string|WordPress_Plugin_Template_Taxonomy
     */
    public function register_taxonomy( $taxonomy = '', $plural = '', $single = '', $post_types = array(), $taxonomy_args = array() ) {

//        if ( ! $taxonomy || ! $plural || ! $single ) {
//            return false;
//        }
//
//        $taxonomy = new WordPress_Plugin_Template_Taxonomy( $taxonomy, $plural, $single, $post_types, $taxonomy_args );
//
//        return $taxonomy;
    }

    /**
     * Load frontend CSS.
     *
     * @access  public
     * @return void
     * @since   1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style( 'styles',  esc_url( $this->assets_url ) . 'css/styles.css');
    } // End enqueue_styles ()

    /**
     * Load frontend Javascript.
     *
     * @access  public
     * @return  void
     * @since   1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script( 'copy', esc_url( $this->assets_url ) . 'js/copy.js' );
    } // End enqueue_scripts ()

    /**
     * Admin enqueue style.
     *
     * @param string $hook Hook parameter.
     *
     * @return void
     */
    public function admin_enqueue_styles( $hook = '' ) {
//        wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
//        wp_enqueue_style( $this->_token . '-admin' );
    } // End admin_enqueue_styles ()

    /**
     * Load admin Javascript.
     *
     * @access  public
     *
     * @param string $hook Hook parameter.
     *
     * @return  void
     * @since   1.0.0
     */
    public function admin_enqueue_scripts( $hook = '' ) {
//        wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version, true );
//        wp_enqueue_script( $this->_token . '-admin' );
    } // End admin_enqueue_scripts ()

    /**
     * Load plugin localisation
     *
     * @access  public
     * @return  void
     * @since   1.0.0
     */
    public function load_localisation() {
        //load_plugin_textdomain( 'wordpress-plugin-template', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
    } // End load_localisation ()

    /**
     * Load plugin textdomain
     *
     * @access  public
     * @return  void
     * @since   1.0.0
     */
    public function load_plugin_textdomain() {
//        $domain = 'wordpress-plugin-template';
//
//        $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
//
//        load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
//        load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
    } // End load_plugin_textdomain ()

    /**
     * Main WordPress_Plugin_Template Instance
     *
     * Ensures only one instance of WordPress_Plugin_Template is loaded or can be loaded.
     *
     * @param string $file File instance.
     * @param string $version Version parameter.
     *
     * @return Object WordPress_Plugin_Template instance
     * @see WordPress_Plugin_Template()
     * @since 1.0.0
     * @static
     */
    public static function instance( $file = '', $version = '1.0.0' ) {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self( $file, $version );
        }

        return self::$_instance;
    } // End instance ()

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, esc_html( __( 'Cloning of WordPress_Plugin_Template is forbidden' ) ), esc_attr( $this->_version ) );

    } // End __clone ()

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, esc_html( __( 'Unserializing instances of WordPress_Plugin_Template is forbidden' ) ), esc_attr( $this->_version ) );
    } // End __wakeup ()

    /**
     * Installation. Runs on activation.
     *
     * @access  public
     * @return  void
     * @since   1.0.0
     */
    public function activate() {
        $this->_log_version_number();


    } // End install ()

    public function deactivate() {
        //$this->_log_version_number();
    } // End install ()

    /**
     * Log the plugin version number.
     *
     * @access  public
     * @return  void
     * @since   1.0.0
     */
    private function _log_version_number() { //phpcs:ignore
        update_option( $this->_token . '_version', $this->_version );
    } // End _log_version_number ()
}