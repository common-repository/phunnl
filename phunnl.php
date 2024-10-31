<?php
/**
 * @package phunnl
 */
/*
/**
 * Plugin Name: Phunnl IVR for WordPress eCommerce
 * Plugin URI: https://phunnl.com/plugins/phunnl
 * Description: phunnl is a WordPress eCommerce extension for that gives small businesses professional IVR services. phunnl drives savings and delivers ROI based on your organization's existing Customer Service workflow.
 * Author: Phunnl, llc.
 * Author URI: https://phunnl.com/
 * Version: 1.0.1
 * License: GPLv2 or later
   Text Domain: phunnl
*/



// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

//make sure woo commerce is installed
//if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    define( 'phunnl_VERSION', '1.0.1' );
    define( 'phunnl__MINIMUM_WP_VERSION', '4.0' );
    define( 'phunnl__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
    define( 'phunnl_DELETE_LIMIT', 100000 );

    register_activation_hook( __FILE__, array( 'phunnl', 'plugin_activation' ) );
    register_deactivation_hook( __FILE__, array( 'phunnl', 'plugin_deactivation' ) );

    require_once( phunnl__PLUGIN_DIR . 'class.phunnl.php' );
    require_once( phunnl__PLUGIN_DIR . 'class.phunnl-widget.php' );


    add_action( 'init', array( 'phunnl', 'init' ) );


    if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	    require_once( phunnl__PLUGIN_DIR . 'class.phunnl-admin.php' );
	    add_action( 'init', array( 'phunnl_Admin', 'init' ) );
    }

    //add wrapper class around deprecated phunnl functions that are referenced elsewhere
    require_once( phunnl__PLUGIN_DIR . 'wrapper.php' );

    if ( defined( 'WP_CLI' ) && WP_CLI ) {
	    require_once( phunnl__PLUGIN_DIR . 'class.phunnl-cli.php' );
    }

//}

