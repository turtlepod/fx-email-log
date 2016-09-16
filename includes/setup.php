<?php
/**
 * Setup Plugin
 * @since 1.0.0
**/
namespace fx_base;
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

/* Functions */
require_once( PATH . 'includes/functions.php' );

/* Post Type & Taxonomy */
require_once( PATH . 'includes/custom-content/custom-content.php' );

/* Settings */
require_once( PATH . 'includes/settings/settings.php' );

