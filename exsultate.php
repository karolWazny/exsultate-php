<?php
/**
 * Plugin Name: E-Xsultate
 * Version: 1.0.0
 * Plugin URI:
 * Description: This is a plugin meant to serve as a base for a digital songbook for DDMuz.
 * Author: Karol Ważny
 * Author URI:
 * Requires at least: 4.0
 *
 * Text Domain: exsultate
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Karol Ważny
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Load plugin class files.
require_once 'classes/class-exsultate.php';
//require_once 'includes/class-wordpress-plugin-template-settings.php';

// Load plugin libraries.
//require_once 'includes/lib/class-wordpress-plugin-template-admin-api.php';
//require_once 'includes/lib/class-wordpress-plugin-template-post-type.php';
//require_once 'includes/lib/class-wordpress-plugin-template-taxonomy.php';

/**
 * Returns the main instance of WordPress_Plugin_Template to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object WordPress_Plugin_Template
 */
function exsultate() {
    $instance = Exsultate::instance( __FILE__, '1.0.0' );

//    if ( is_null( $instance->settings ) ) {
//        $instance->settings = WordPress_Plugin_Template_Settings::instance( $instance );
//    }

    return $instance;
}

define( 'MY_LZB_PATH', __DIR__ . '/inc/lzb/' );
define( 'MY_LZB_URL', plugin_dir_url(__FILE__) . 'inc/lzb/' );

// Include the LZB plugin.
require_once MY_LZB_PATH . 'lazy-blocks.php';

// Customize the url setting to fix incorrect asset URLs.
add_filter( 'lzb/plugin_url', 'my_lzb_url' );
function my_lzb_url( $url ) {
    return MY_LZB_URL;
}

exsultate();
