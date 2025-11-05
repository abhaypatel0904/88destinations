<?php
/**
 * Class Custom_Taxonomies
 *
 * Handles custom taxonomy registration.
 */
class Custom_Taxonomies {

	/**
	 * Instance holder.
	 *
	 * @var Custom_Taxonomies|null
	 */
	protected static $_instance = null;

	/**
	 * Get class instance.
	 *
	 * @return Custom_Taxonomies
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Register hooks.
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'register_location_taxonomy' ) );
	}

	/**
	 * Register custom taxonomy: Location for Tour post type.
	 *
	 * @return void
	 */
	public function register_location_taxonomy() {

		$labels = array(
			'name'              => _x( 'Locations', 'taxonomy general name', 'text-domain' ),
			'singular_name'     => _x( 'Location', 'taxonomy singular name', 'text-domain' ),
			'search_items'      => _x( 'Search Locations', 'taxonomy search label', 'text-domain' ),
			'all_items'         => _x( 'All Locations', 'taxonomy all items', 'text-domain' ),
			'parent_item'       => _x( 'Parent Location', 'taxonomy parent item', 'text-domain' ),
			'parent_item_colon' => _x( 'Parent Location:', 'taxonomy parent item colon', 'text-domain' ),
			'edit_item'         => _x( 'Edit Location', 'taxonomy edit item', 'text-domain' ),
			'update_item'       => _x( 'Update Location', 'taxonomy update item', 'text-domain' ),
			'add_new_item'      => _x( 'Add New Location', 'taxonomy add new item', 'text-domain' ),
			'new_item_name'     => _x( 'New Location Name', 'taxonomy new item name', 'text-domain' ),
			'menu_name'         => _x( 'Locations', 'taxonomy menu name', 'text-domain' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'location' ),
			'show_in_rest'      => true,
		);

		// Register taxonomy only if 'register_taxonomy' is callable.
		if ( ! is_callable( 'register_taxonomy' ) ) {
			return;
		}

		register_taxonomy( 'location', array( 'tour' ), $args );
	}
}

// Initialize class instance.
Custom_Taxonomies::get_instance();
