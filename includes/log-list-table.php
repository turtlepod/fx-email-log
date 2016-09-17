<?php
namespace fx_email_log;
if ( ! defined( 'WPINC' ) ) { die; }

/* Include WP List Table Class */
require_once( PATH . 'library/list-table-class.php' );

/**
 * Table to display Email Logs.
 * Based on Custom List Table Example by Matt Van Andel.
 * @link https://wordpress.org/plugins/custom-list-table-example/
 */
class Log_List_Table extends List_Table{

	/**
	 * Constuctor
	 */
	public function __construct() {
		parent::__construct( array(
			'singular'  => 'fx-email-log',
			'plural'    => 'fx-email-logs',
			'ajax'      => false,
		) );
	}

	/* Columns Config
	------------------------------------------ */

	/**
	 * REQUIRED: Returns the list of column and title names.
	 * @see WP_List_Table::::single_row_columns()
	 * @return array An associative array containing column information: 'slugs'=>'Visible Titles'.
	 */
	public function get_columns() {
		$columns = array(
			'cb'        => '<input type="checkbox" />',
			'sent_date' => __( 'Sent at', 'fx-email-log' ),
			'to'        => __( 'To', 'fx-email-log' ),
			'subject'   => __( 'Subject', 'fx-email-log' ),
		);
		return $columns;
	}

	/**
	 * Returns the list of sortable columns.
	 * @return array An associative array containing all the columns that should be sortable:
	 * `'slugs' => array( 'data_values', bool ).`
	 */
	protected function get_sortable_columns() {
		$sortable_columns = array(
			'sent_date'   => array( 'sent_date', true ), //true means it's already sorted
			'to'          => array( 'to_email', false ),
			'subject'     => array( 'subject', false ),
		);
		return $sortable_columns;
	}

	/* Each Columns
	------------------------------------------ */

	/**
	 * Returns value for default columns.
	 * This is loaded if the columns callback function not exist.
	 */
	protected function column_default( $item, $column_name ) {
		switch( $column_name ){
			default:
				return print_r( $item, true );
		}
	}

	/**
	 * Display sent date column.
	 */
	protected function column_sent_date( $item ) {

		$email_date = mysql2date(
			sprintf( '%s @ %s', get_option( 'date_format', 'F j, Y' ), get_option( 'time_format', 'g:i A' ) ),
			$item->sent_date
		);

		/* View Content URL (AJAX) */
		$view_content_url = add_query_arg(
			array(
				'action'                         => 'fx_email_log_view_content',
				'email_id'                       => esc_html( $item->id ),
				'TB_iframe'                      => 'true',
				'width'                          => '600',
				'height'                         => '550',
			),
			'admin-ajax.php'
		);

		/* Delete Item URL */
		$delete_url = add_query_arg(
			array(
				'page'                           => esc_html( $_REQUEST['page'] ),
				'action'                         => 'delete',
				$this->_args['singular']         => esc_html( $item->id ),
				'_fx_email_log_delete_nonce'     => wp_create_nonce( 'fx_email_log_delete_nonce' ),
			)
		);

		/* Row Actions */
		$actions = array(
			'view-content' => sprintf( '<a href="%1$s" class="thickbox" title="%2$s">%3$s</a>',
				esc_url( $view_content_url ),
				__( 'Email Content', 'fx-email-log' ),
				__( 'View Content', 'fx-email-log' )
			),
			'delete' => sprintf( '<a href="%s">%s</a>',
				esc_url( $delete_url ),
				__( 'Delete', 'fx-email-log' )
			),
		);

		/* Output */
		return sprintf( '%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
			/*$1%s*/ esc_html( $email_date ),
			/*$2%s*/ esc_html( $item->id ),
			/*$3%s*/ $this->row_actions( $actions )
		);
	}

	/**
	 * Display To field.
	 */
	protected function column_to( $item ) {
		return esc_html( $item->to_email );
	}

	/**
	 * Display Subject field.
	 */
	protected function column_subject( $item ) {
		return esc_html( $item->subject );
	}

	/**
	 * Display markup for action column.
	 */
	protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ esc_html( $this->_args['singular'] ),
			/*$2%s*/ esc_html( $item->id )
		);
	}

	/* Bulk Actions
	------------------------------------------ */

	/**
	 * Specify the list of bulk actions.
	 * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'.
	 */
	protected function get_bulk_actions() {
		$actions = array(
			'delete'     => __( 'Delete', 'fx-email-log' ),
			'delete-all' => __( 'Delete All Logs', 'fx-email-log' ),
		);
		return $actions;
	}

	/**
	 * Handles bulk actions.
	 * @see $this->prepare_items()
	 */
	public function process_bulk_action() {

		/* Bail if no action */
		if( ! $this->current_action() ){
			return;
		}

		global $wpdb;
		$table_name = "{$wpdb->prefix}fx_email_log";

		/* Bulk Delete Actions */
		if( in_array( $this->current_action(), array( 'delete', 'delete-all' ) ) ){

			/* Check nonce and caps */
			if ( wp_verify_nonce( $_REQUEST['_fx_email_log_delete_nonce'], 'fx_email_log_delete_nonce' ) && current_user_can( 'manage_options' ) ) {

				/* Delete */
				if( 'delete' == $this->current_action() ){

					/* Get IDs */
					$ids = $_GET[$this->_args['singular']];
					$ids = is_array( $ids ) ? implode( ',', $ids ) : $ids;
					$ids = esc_sql( $ids );

					/* Delete it */
					$success = $wpdb->query( "DELETE FROM {$table_name} where id IN ( {$ids} )" );
				}

				/* Delete All */
				elseif( 'delete-all' == $this->current_action() ){
					$success = $wpdb->query( "DELETE FROM {$table_name}" );
				}

				/* Add Updated Message */
				if( false !== $success ){
					add_settings_error(
						$settings = 'fx_email_log',
						$code = '',
						$message = __( 'Email Log Deleted.', 'fx-email-log' ),
						$type = 'updated'
					);
				}
				else{
					add_settings_error(
						$settings = 'fx_email_log',
						$code = '',
						$message = __( 'Fail to Delete Email Log. Please try again.', 'fx-email-log' ),
						$type = 'error'
					);
				}
			}
			/* Nonce & Caps Check Fail */
			else {
				wp_die(
					'<h1>' . __( 'Cheatin&#8217; uh?', 'fx-email-log' ) . '</h1>' .
					'<p>' . __( 'Sorry, you are not allowed to delete email log.', 'fx-email-log' ) . '</p>',
					403
				);
			}
		}
	}

	/* Prepare Table
	------------------------------------------ */

	/**
	 * Prepare data for display.
	 */
	public function prepare_items() {
		global $wpdb;
		$table_name = "{$wpdb->prefix}fx_email_log";
		$this->_column_headers = $this->get_column_info();

		/* Process Bulk Action */
		$this->process_bulk_action();

		/* START Query DB */
		$query = "SELECT * FROM {$table_name}";
		$count_query = "SELECT count(*) FROM {$table_name}";
		$query_cond = '';

		/* Search email and subject */
		if ( isset( $_GET['s'] ) ) {
			$search_term = trim( esc_sql( $_GET['s'] ) );
			$query_cond .= " WHERE to_email LIKE '%{$search_term}%' OR subject LIKE '%{$search_term}%' ";
		}

		/* Sortable: Order & Orderby Query */
		$orderby = ! empty( $_GET['orderby'] ) ? esc_sql( $_GET['orderby'] ) : 'sent_date';
		$order   = ! empty( $_GET['order'] ) ? esc_sql( $_GET['order'] ) : 'DESC';
		$query_cond .= " ORDER BY {$orderby} {$order}";

		/* Get Total Items */
		$count_query = $count_query . $query_cond;
		$total_items = $wpdb->get_var( $count_query );

		/* Pagination */
		$per_page = intval( $this->get_per_page() );
		$current_page = $this->get_pagenum();
		if ( ! empty( $current_page ) && ! empty( $per_page ) ) {
			$offset = ( $current_page - 1 ) * $per_page;
			$offset = intval( $offset );
			$query_cond .= " LIMIT {$offset},{$per_page}";
		}

		/* Fetch the items */
		$query = $query . $query_cond;
		$this->items = $wpdb->get_results( $query );

		/* Register pagination options & calculations. */
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page ),
		) );
	}


	/* View Content Ajax Callback
	------------------------------------------ */




	/* Utility
	------------------------------------------ */
	
	/**
	 * Gets the per page option.
	 */
	public static function get_per_page() {
		$screen = get_current_screen();
		$option = $screen->get_option( 'per_page', 'option' );
		$per_page = get_user_meta( get_current_user_id(), $option, true );
		if ( empty( $per_page ) || $per_page < 1 ) {
			$per_page = $screen->get_option( 'per_page', 'default' );
		}
		return $per_page;
	}

	/**
	 * Displays default message when no items are found.
	 */
	public function no_items() {
		_e( 'Your email log is empty', 'fx-email-log' );
	}
}