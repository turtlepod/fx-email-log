<?php
namespace fx_base;
if ( ! defined( 'WPINC' ) ) { die; }
Stuff::get_instance();

/**
 * Stuff
 * @since 1.0.0
 */
class Stuff{

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

		/* Stuff */
	}

}
