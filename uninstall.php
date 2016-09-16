<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

/* Clean up stuff */
delete_option( 'fx-base' );
delete_option( 'fx-base_welcome' );