<?php
/**
 * Plugin Name: f(x) Email Log
 * Plugin URI: http://genbumedia.com/plugins/fx-email-log/
 * Description: Simple plugin to log all email sent via WordPress.
 * Version: 1.0.0
 * Author: David Chandra Purnama
 * Author URI: http://shellcreeper.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: fx-email-log
 * Domain Path: /languages/
 *
 * @author David Chandra Purnama <david@genbumedia.com>
 * @copyright Copyright (c) 2016, Genbu Media
 * 
 * This plugin is based on Email Log Plugin by Sudar Muthu
 * Released under GNU General Public License, version 2.
 * Copyright 2009  Sudar Muthu  (email : sudar@sudarmuthu.com)
 * http://sudarmuthu.com/wordpress/email-log
 * 
**/
if ( ! defined( 'WPINC' ) ) { die; }


/* Init
------------------------------------------ */

/* Load plugin in "plugins_loaded" hook */
add_action( 'plugins_loaded', 'fx_email_log_init' );

/**
 * Plugin Init
 * @since 0.1.0
 */
function fx_email_log_init(){

	/* Var */
	$uri      = trailingslashit( plugin_dir_url( __FILE__ ) );
	$path     = trailingslashit( plugin_dir_path( __FILE__ ) );
	$file     = __FILE__;
	$plugin   = plugin_basename( __FILE__ );
	$version  = '1.0.0';

	/* Prepare */
	require_once( $path . 'includes/prepare.php' );
	if( ! $sys_req->check() ) return;

	/* Setup */
	require_once( $path . 'includes/setup.php' );
}


/* Activation
------------------------------------------ */

/* Register activation hook. */
register_activation_hook( __FILE__, 'fx_email_log_plugin_activation' );

/**
 * Runs only when the plugin is activated.
 * @since 1.0.0
 */
function fx_email_log_plugin_activation() {
	require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'install.php' );
}
