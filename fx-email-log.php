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


/* Constants
------------------------------------------ */

define( 'FX_EMAIL_LOG_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'FX_EMAIL_LOG_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'FX_EMAIL_LOG_FILE', __FILE__;
define( 'FX_EMAIL_LOG_PLUGIN', plugin_basename( __FILE__ );
define( 'FX_EMAIL_LOG_VERSION', '1.0.0' );


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
	$uri      = FX_EMAIL_LOG_URI;
	$path     = FX_EMAIL_LOG_PATH;
	$file     = FX_EMAIL_LOG_FILE;
	$plugin   = FX_EMAIL_LOG_PLUGIN;
	$version  = FX_EMAIL_LOG_VERSION;

	/* Prepare */
	require_once( $path . 'includes/prepare.php' );
	if( ! $sys_req->check() ) return;

	/* Setup */
	require_once( $path . 'includes/setup.php' );
}


/* Activation
------------------------------------------ */

/* Multisite Compat */
if( is_multisite() ){
	require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/multisite.php' );
}

/* Register activation hook. */
register_activation_hook( __FILE__, 'fx_email_log_plugin_activation' );

/**
 * Runs only when the plugin is activated.
 * @since 1.0.0
 */
function fx_email_log_plugin_activation( $network_wide ) {
	require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'install.php' );
}

/**
 * Create Table
 * @since 1.0.0
 */
function fx_email_log_create_table(){
	global $wpdb;

	$table_name = "{$wpdb->prefix}fx_email_log";
	$charset_collate = $wpdb->get_charset_collate();

	/* Check if table exist */
	if ( $wpdb->get_var( "show tables like '{$table_name}'" ) != $table_name ) {

		/* Create DB Table */
		$sql = 'CREATE TABLE ' . $table_name . ' (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			to_email VARCHAR(100) NOT NULL,
			subject VARCHAR(250) NOT NULL,
			message TEXT NOT NULL,
			headers TEXT NOT NULL,
			attachments TEXT NOT NULL,
			sent_date timestamp NOT NULL,
			PRIMARY KEY  (id)
		) ' . $charset_collate . ' ;';

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		/* Save DB Version Number */
		update_option( 'fx_email_log_db_version', FX_EMAIL_LOG_VERSION );
	}
}
