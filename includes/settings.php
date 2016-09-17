<?php
namespace fx_email_log;
if ( ! defined( 'WPINC' ) ) { die; }
Settings::get_instance();

/**
 * Log Admin Page
 * @since 1.0.0
 */
class Settings{

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

		/* Create Settings Page */
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
	}


	/**
	 * Create Settings Page
	 * @since 1.0.0
	 */
	public function add_settings_page(){

		/* Create Settings Sub-Menu */
		add_submenu_page(
			$parent_slug = 'tools.php',
			$page_title  = __( 'f(x) Email Log', 'fx-email-log' ),
			$menu_title  = __( 'f(x) Email Log', 'fx-email-log' ),
			$capability  = 'manage_options',
			$menu_slug   = 'fx_email_log',
			$function    = array( $this, 'settings_page' )
		);

		/* Prepare Settings */
		add_action( 'load-tools_page_fx_email_log', array( $this, 'prepare_settings' ) );
	}


	/**
	 * Prepare Settings
	 */
	public function prepare_settings(){
		require_once( PATH . 'includes/log-list-table.php' );
		$this->log_table = new Log_List_Table();
		$this->log_table->prepare_items();
	}


	/**
	 * Settings Page Output
	 * @since 1.0.0
	 */
	public function settings_page(){
		add_thickbox();
		?>
		<div class="wrap">

			<h1><?php _e( 'f(x) Email Log', 'fx-email-log' ); ?></h1>

			<?php settings_errors(); ?>

			<form method="get">
				<?php $this->log_table->display(); ?>
			</form>

		</div><!-- wrap -->
		<?php
	}





}

