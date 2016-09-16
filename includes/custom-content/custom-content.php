<?php
namespace fx_base;
if ( ! defined( 'WPINC' ) ) { die; }
Custom_Content::get_instance();

/**
 * Custom Content: Post Type & Taxonomy
 * @since 1.0.0
 */
class Custom_Content{

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

		/* Var */
		$this->uri  = trailingslashit( plugin_dir_url( __FILE__ ) );
		$this->path = trailingslashit( plugin_dir_path( __FILE__ ) );

		/* Register Post Type & Taxonomy */
		add_action( 'init', array( $this, 'register' ) );

		/* Admin Scripts */
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Register Post Type & Taxonomy
	 */
	public function register(){

		/* Custom Post Type
		------------------------------------------ */
		$cpt_args = array(
			'description'           => '',
			'public'                => true,
			'publicly_queryable'    => true,
			'show_in_nav_menus'     => true,
			'show_in_admin_bar'     => true,
			'exclude_from_search'   => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 3,
			'menu_icon'             => 'dashicons-screenoptions',
			'can_export'            => true,
			'delete_with_user'      => false,
			'hierarchical'          => false,
			'has_archive'           => true, 
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'fx-base', 'with_front' => false ),
			'capability_type'       => 'page',
			'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
			'labels'                => array(
				'name'                      => __( 'Stuffs', 'fx-base' ),
				'singular_name'             => __( 'Stuff', 'fx-base' ),
				'add_new'                   => __( 'Add New', 'fx-base' ),
				'add_new_item'              => __( 'Add New Item', 'fx-base' ),
				'edit_item'                 => __( 'Edit Item', 'fx-base' ),
				'new_item'                  => __( 'New Item', 'fx-base' ),
				'all_items'                 => __( 'All Items', 'fx-base' ),
				'view_item'                 => __( 'View Item', 'fx-base' ),
				'search_items'              => __( 'Search Items', 'fx-base' ),
				'not_found'                 => __( 'Not Found', 'fx-base' ),
				'not_found_in_trash'        => __( 'Not Found in Trash', 'fx-base' ), 
				'menu_name'                 => __( 'f(x) Base', 'fx-base' ),
			),
		);
		register_post_type( 'fx_base', $cpt_args );


		/* Custom Taxonomy
		------------------------------------------ */
		$ctax_args = array(
			'public'            => true,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'query_var'         => true,
			'labels' => array(
				'name'                       => __( 'Stuff Cats', 'fx-base' ),
				'singular_name'              => __( 'Stuff Cat', 'fx-base' ),
				'name_admin_bar'             => __( 'Cat', 'fx-base' ),
				'search_items'               => __( 'Search Items', 'fx-base' ),
				'popular_items'              => __( 'Popular Items', 'fx-base' ),
				'all_items'                  => __( 'All Items', 'fx-base' ),
				'edit_item'                  => __( 'Edit Item', 'fx-base' ),
				'view_item'                  => __( 'View Item', 'fx-base' ),
				'update_item'                => __( 'Update Item', 'fx-base' ),
				'add_new_item'               => __( 'Add New Item', 'fx-base' ),
				'new_item_name'              => __( 'New Item Name', 'fx-base' ),
				'separate_items_with_commas' => __( 'Separate items with commas', 'fx-base' ),
				'add_or_remove_items'        => __( 'Add or remove items', 'fx-base' ),
				'choose_from_most_used'      => __( 'Choose from the most used items', 'fx-base' ),
				'menu_name'                  => __( 'Cats', 'fx-base' ),
			),
		);
		register_taxonomy( 'fx_base_cat', array( 'fx_base' ), $ctax_args );
	}


	/**
	 * Admin Scripts
	 */
	public function admin_scripts( $hook_suffix ){
		global $post_type, $taxonomy;

		/* Register */
		wp_register_style( 'fx-base-admin', $this->uri . 'assets/style.css', array(), VERSION );
		wp_register_script( 'fx-base-admin', $this->uri . 'assets/script.js', array( 'jquery' ), VERSION, true );

		/* Post Type Screen */
		if( 'fx_base' == $post_type ){

			/* Columns/List */
			if( 'edit.php' == $hook_suffix ){
				wp_enqueue_style( 'fx-base-admin' );
				wp_enqueue_script( 'fx-base-admin' );
			}

			/* Add/Edit Screen */
			if( in_array( $hook_suffix, array( 'post-new.php', 'post.php' ) ) ){
				wp_enqueue_style( 'fx-base-admin' );
				wp_enqueue_script( 'fx-base-admin' );
			}
		}

		/* Taxonomy Screen */
		if( 'fx_base_cat' == $taxonomy ){

			/* Add New & Column/List */
			if( "edit-tags.php" == $hook_suffix ){
				wp_enqueue_style( 'fx-base-admin' );
				wp_enqueue_script( 'fx-base-admin' );
			}

			/* Edit Screen */
			if( "term.php" == $hook_suffix ){
				wp_enqueue_style( 'fx-base-admin' );
				wp_enqueue_script( 'fx-base-admin' );
			}
		}

	}

}