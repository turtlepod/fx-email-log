<?php
/**
 * Setup Plugin
 * @since 1.0.0
**/
namespace fx_email_log;
if ( ! defined( 'WPINC' ) ) { die; }

/* Constants
------------------------------------------ */

define( __NAMESPACE__ . '\URI', $uri );
define( __NAMESPACE__ . '\PATH', $path );
define( __NAMESPACE__ . '\FILE', $file );
define( __NAMESPACE__ . '\PLUGIN', $plugin );
define( __NAMESPACE__ . '\VERSION', $version );


/* Load Files
------------------------------------------ */

/* Log Email */
require_once( PATH . 'includes/log-email.php' );

/* Settings */
require_once( PATH . 'includes/settings.php' );

