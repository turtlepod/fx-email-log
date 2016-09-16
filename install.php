<?php
if ( ! defined( 'WPINC' ) ) { die; }
global $wpdb;

/* Multisite
------------------------------------------ */
if ( is_multisite() && $network_wide ) {

	/* Loop through all sub-site and create table for each. */
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
	foreach ( $blog_ids as $blog_id ) {
		switch_to_blog( $blog_id );
		fx_email_log_create_table();
		restore_current_blog();
	}

}

/* Single Site Install
------------------------------------------ */
else {
	fx_email_log_create_table();
}
