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

		/* Save Screen Options */
		add_filter( 'set-screen-option', array( $this, 'save_screen_options' ), 10, 3 );

		/* Enqueue Script */
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );

		/* Ajax Callback To View Email Content */
		add_action( 'wp_ajax_fx_email_log_view_content', array( $this, 'ajax_view_email_content' ) );
	}


	/**
	 * Create Settings Page
	 * @since 1.0.0
	 */
	public function add_settings_page(){

		/* Create Settings Sub-Menu */
		$admin_page = add_submenu_page(
			$parent_slug = 'tools.php',
			$page_title  = __( 'Email Log', 'fx-email-log' ),
			$menu_title  = __( 'Email Log', 'fx-email-log' ),
			$capability  = 'manage_options',
			$menu_slug   = 'fx_email_log',
			$function    = array( $this, 'settings_page' )
		);

		/* Prepare Settings */
		add_action( "load-{$admin_page}", array( $this, 'prepare_settings' ) );
	}


	/**
	 * Prepare Settings
	 */
	public function prepare_settings(){

		/* Check User Caps */
		if ( ! current_user_can( 'manage_options' ) ){
			return;
		}

		/* Remove Welcome */
		if( ! get_option( 'fx-email-log_welcome' ) ){
			update_option( 'fx-email-log_welcome', 1 );
		}

		/* Get Current Screen/Page */
		$screen = get_current_screen();

		/* Add Per Page Options */
		$screen->add_option(
			'per_page',
			array(
				'label' => __( 'Entries per page', 'fx-email-log' ),
				'default' => 20,
				'option' => 'fx_email_log_per_page',
			)
		);

		/* Include List Table Abstract Class */
		require_once( PATH . 'includes/log-list-table.php' );

		/* Load Table Class */
		$this->log_table = new Log_List_Table();
		$this->log_table->prepare_items();
	}


	/**
	 * Settings Page Output
	 * @since 1.0.0
	 */
	public function settings_page(){
		?>
		<div id="fx-email-log-settings-wrap" class="wrap">

			<h1><?php _e( 'Email Log', 'fx-email-log' ); ?></h1>

			<?php settings_errors(); ?>

			<form method="get">
				<?php $this->log_table->search_box( __( 'Search Logs', 'fx-email-log' ), 'search_id' ); ?>
				<input type="hidden" name="page" value="<?php echo esc_html( $_GET['page'] ); ?>" />
			</form>

			<form method="get">
				<?php $this->log_table->display(); ?>
				<?php wp_nonce_field( "fx_email_log_delete_nonce", "_fx_email_log_delete_nonce" ); ?>
				<input type="hidden" name="page" value="<?php echo esc_html( $_GET['page'] ); ?>" />
			</form>

			<div id="fx-email-log-modal-overlay" style="display:none;"></div><!-- #fx-email-log-modal-overlay -->
			<div id="fx-email-log-modal" style="display:none;width:850px;height:500px;">
				<div id="fx-email-log-modal-container">
					<div id="fx-email-log-modal-title"><?php _e( 'Email Content', 'fx-email-log' ); ?><span class="fx-email-log-modal-close"></span></div>
					<div id="fx-email-log-modal-content">
						<iframe id="fx-email-log-iframe" height="100%"; width="100%" scrolling="yes"></iframe>
					</div>
				</div>
			</div><!-- #fx-email-log-modal -->

		</div><!-- wrap -->
		<?php
	}


	/**
	 * Save Screen Option (Per Page)
	 */
	public function save_screen_options( $status, $option, $value ) {
		if ( 'fx_email_log_per_page' == $option ) {
			return $value;
		}
		else {
			return $status;
		}
	}

	/**
	 * Settings Scripts
	 * @since 1.0.2
	 */
	public function scripts( $hook_suffix ){
		if( 'tools_page_fx_email_log' !== $hook_suffix ){
			return;
		}
		wp_enqueue_style( 'fx-email-log-settings', URI . "assets/settings.css", array(), VERSION );
		wp_enqueue_script( 'fx-email-log-settings', URI . "assets/settings.js", array( 'jquery' ), VERSION, true );
		$data = array(
			'nonce'       => wp_create_nonce( 'fx-email-log-view-content' ),
			'reset_css'   => esc_url( URI . "assets/reset.css" ),
		);
		wp_localize_script( 'fx-email-log-settings', 'fx_email_log', $data );
	}


	/**
	 * Ajax Callback to View Email Content
	 * @since 1.0.0
	 */
	public function ajax_view_email_content(){

		/* Get Request */
		$request = stripslashes_deep( $_POST );

		/* Check validation */
		check_ajax_referer( 'fx-email-log-view-content', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			echo wpautop( "You don't have permission to view this content.", 'fx-email-log' );
			wp_die();
		}

		/* Check Email ID */
		$email_id = isset( $request['email_id'] ) ? $request['email_id'] : false;
		if( ! $email_id ){
			echo wpautop( "Email ID Not Set.", 'fx-email-log' );
			wp_die();
		}

		/* Display Email Content */
		global $wpdb;

		$table_name = "{$wpdb->prefix}fx_email_log";

		$query   = $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $email_id );
		$content = $wpdb->get_results( $query );

		if ( false !== strpos( $content[0]->headers, 'html' ) ){
			$message = $content[0]->message;
		}
		else{
			$message = wpautop( $content[0]->message );
		}
		echo wp_kses_post( $message );

		wp_die();
	}

}