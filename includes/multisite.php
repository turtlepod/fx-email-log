<?php
/* Multisite
------------------------------------------ */

/* Create New Blog */
add_action( 'wpmu_new_blog', 'fx_email_log_multisite_new_blog', 10, 6 );

/* Delete Blog */
add_filter( 'wpmu_drop_tables', 'fx_email_log_multisite_delete_blog' );


/**
 * Create Table When New Blog Created
 * @since 1.0.0
 */
function fx_email_log_multisite_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ){
	if ( is_plugin_active_for_network( FX_EMAIL_LOG_PLUGIN ) ) {
		switch_to_blog( $blog_id );
		fx_email_log_create_table();
		restore_current_blog();
	}
}

/**
 * Delete Table When Blog Deleted
 * Add table to delete table list.
 * @since  1.0.0
 */
function fx_email_log_multisite_delete_blog( $tables ) {
	global $wpdb;
	$tables[] = "{$wpdb->prefix}fx_email_log";
	return $tables;
}
