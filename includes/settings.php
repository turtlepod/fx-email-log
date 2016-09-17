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

		/* Ajax Callback To View Email Content */
		add_action( 'wp_ajax_fx_email_log_view_content', array( $this, 'view_content_ajax_callback' ) );
	}

	/**
	 * Create Settings Page
	 * @since 1.0.0
	 */
	public function add_settings_page(){

		/* Create Settings Sub-Menu */
		$admin_page = add_submenu_page(
			$parent_slug = 'tools.php',
			$page_title  = __( 'f(x) Email Log', 'fx-email-log' ),
			$menu_title  = __( 'f(x) Email Log', 'fx-email-log' ),
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

		/* Get Current Screen/Page */
		$screen = get_current_screen();

		/* Add Per Page Options */
		$screen->add_option(
			'per_page',
			array(
				'label' => __( 'Entries per page', 'fx-email-log' ),
				'default' => 20,
				'option' => 'per_page',
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
		add_thickbox();
		?>
		<div class="wrap">

			<h1><?php _e( 'f(x) Email Log', 'fx-email-log' ); ?></h1>

			<?php settings_errors(); ?>

			<form method="get">
				<?php $this->log_table->display(); ?>
				<?php wp_nonce_field( "fx_email_log_delete_nonce", "_fx_email_log_delete_nonce" ); ?>
				<input type="hidden" name="page" value="<?php echo esc_html( $_GET['page'] ); ?>" />
			</form>

		</div><!-- wrap -->
		<?php
	}

	/**
	 * Ajax Callback to Iframe Load Content
	 * @since 1.0.0
	 */
	public function view_content_ajax_callback(){
		global $wpdb;

		if ( current_user_can( 'manage_options' ) ) {
			$table_name = "{$wpdb->prefix}fx_email_log";
			$email_id   = absint( $_GET['email_id'] );

			$query   = $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $email_id );
			$content = $wpdb->get_results( $query );

			$message = $content[0]->message;
			if ( false !== strpos( $content[0]->headers, 'text/plain' ) ){
				$message = wpautop( $message );
			}
			echo $message;
		}

		wp_die();
	}

}