<?php
namespace fx_email_log;
if ( ! defined( 'WPINC' ) ) { die; }
Log_Email::get_instance();

/**
 * Log Email
 * @since 1.0.0
 */
class Log_Email{

	/**
	 * Returns the instance.
	 */
	public static function get_instance(){
		static $instance = null;
		if ( is_null( $instance ) ) $instance = new self;
		return $instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {

		/* Logs email to database */
		add_filter( 'wp_mail', array( $this, 'log_email' ) );
	}

	/**
	 * Logs email to database.
	 */
	public function log_email( $mail_info ) {
		global $wpdb;
		$table_name = "{$wpdb->prefix}fx_email_log";

		/* Format Email Data */
		$email_data = array(
			'to_email'    => is_array( $mail_info['to'] ) ? implode( ',', $mail_info['to'] ) : $mail_info['to'],
			'subject'     => $mail_info['subject'],
			'message'     => isset( $mail_info['message'] ) ? $mail_info['message'] : '',
			'headers'     => is_array( $mail_info['headers'] ) ? implode( "\n", $mail_info['headers'] ) : $mail_info['headers'],
			'attachments' => ( count( $mail_info['attachments'] ) > 0 ) ? 'true' : 'false',
			'sent_date'   => current_time( 'mysql' ),
		);

		/* Add to database */
		$wpdb->insert( $table_name, $email_data );

		return $mail_info;
	}

}