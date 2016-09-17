<?php
if ( ! defined( 'WPINC' ) ) { die; }


/* Load Text Domain
------------------------------------------ */
load_plugin_textdomain( dirname( $plugin ), false, dirname( $plugin ) . '/languages/' );


/* Add Support Link
------------------------------------------ */
require_once( $path . 'library/plugin-action-links.php' );
$args = array(
	'plugin'    => $plugin,
	'name'      => __( 'f(x) Email Log', 'fx-email-log' ),
	'version'   => $version,
	'text'      => __( 'Get Support', 'fx-email-log' ),
);
new Fx_Email_Log_Plugin_Action_Links( $args );


/* Check PHP and WordPress Version
------------------------------------------ */
require_once( $path . 'library/system-requirement.php' );
$args = array(
	'wp_requires'   => array(
		'version'       => '4.4',
		'notice'        => wpautop( sprintf( __( 'f(x) Email Log plugin requires at least WordPress 4.4+. You are running WordPress %s. Please upgrade and try again.', 'fx-email-log' ), get_bloginfo( 'version' ) ) ),
	),
	'php_requires'  => array(
		'version'       => '5.3',
		'notice'        => wpautop( sprintf( __( 'f(x) Email Log plugin requires at least PHP 5.3+. You are running PHP %s. Please upgrade and try again.', 'fx-email-log' ), PHP_VERSION ) ),
	),
);
$sys_req = new Fx_Email_Log_System_Requirement( $args );
if( ! $sys_req->check() ) return;


/* Welcome Notice
------------------------------------------ */
require_once( $path . 'library/welcome-notice.php' );
$args = array( 
	'notice'  => wpautop( sprintf( __( 'Your email logs will be available in <a href="%s">Tools > Email Log</a>.', 'fx-email-log' ), esc_url( add_query_arg( 'page', 'fx_email_log', admin_url( 'tools.php' ) ) ) ) ),
	'dismiss' => __( 'Dismiss this notice.', 'fx-email-log' ),
	'option'  => 'fx-email-log_welcome',
);
new Fx_Email_Log_Welcome_Notice( $args );
