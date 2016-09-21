<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit(); };
global $wpdb;

/* Options
------------------------------------------ */
delete_option( 'fx-email-log_welcome' );

/* Multisite
------------------------------------------ */
if ( is_multisite() ) {

	/* Loop through all sub-site and delete table in each. */
	$original_blog_id = get_current_blog_id();
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
	foreach ( $blog_ids as $blog_id ) {
		switch_to_blog( $blog_id );
		fx_email_log_delete_table();
	}
	switch_to_blog( $original_blog_id );

}

/* Single Site Install
------------------------------------------ */
else {
	fx_email_log_delete_table();
}
