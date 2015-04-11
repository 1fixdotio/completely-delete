<?php
/**
 * Completely Delete.
 *
 * @package   Completely_Delete_Admin
 * @author    1fixdotio <1fixdotio@gmail.com>
 * @license   GPL-2.0+
 * @link      http://1fix.io/completely-delete
 * @copyright 2013 1Fix
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-plugin-name.php`
 *
 * @package Completely_Delete_Admin
 * @author  1fixdotio <1fixdotio@gmail.com>
 */
class Completely_Delete_Admin {

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * Call $plugin_slug from public plugin class later.
	 *
	 * @since    0.8.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = null;

	/**
	 * Instance of this class.
	 *
	 * @since    0.1
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    0.1
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	protected $options = array();

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     0.1
	 */
	private function __construct() {

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 */
		$plugin = Completely_Delete::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		require_once( plugin_dir_path( __FILE__ ) . 'includes/settings.php' );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * Define custom functionality.
		 */
		add_action( 'post_submitbox_start', array( $this, 'add_delete_button' ) );
		add_action( 'admin_action_completely_delete', array( $this, 'completely_delete' ) );
		add_action( 'untrash_post', array( $this, 'untrash_post' ) );

		$options = $this->get_options();
		if ( isset( $options['delete_attachments'] ) && 'on' == $options['delete_attachments'] ) {
			add_action( 'before_delete_post', array( $this, 'before_delete_post' ) );
		}

		add_filter( 'post_row_actions', array( $this, 'row_actions' ), 10, 2 );
		add_filter( 'page_row_actions', array( $this, 'row_actions' ), 10, 2 );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.1
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     0.1
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Completely_Delete::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     0.1
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Completely_Delete::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    0.1
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Completely Delete Settings', $this->plugin_slug ),
			__( 'Completely Delete', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    0.1
	 */
	public function display_plugin_admin_page() {

		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    0.1
	 *
	 * @param array<string> $links Action links
	 * @return  array<string> Action links
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings' ) . '</a>',
			),
			$links
		);

	}

	/**
	 * Build completely delete action url.
	 *
	 * @since 0.3
	 *
	 * @param  int    $post_id Post ID
	 * @return string    Action url
	 */
	public function get_action_url( $post_id ) {

		return wp_nonce_url( add_query_arg( array( 'action' => 'completely_delete', 'post' => $post_id ), admin_url( 'admin.php' ) ) );
	}

	/**
	 * Create completely delete link / button.
	 *
	 * @since    0.2
	 *
	 * @return   string The HTML of delete button
	 */
	public function add_delete_button() {

		global $post;
		$post_type_object = get_post_type_object( $post->post_type );

		if ( isset( $_GET['post'] ) && current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {
			?>
				<div id="cd-action">
					<a class="submitcd delete" href="<?php echo $this->get_action_url( $_GET['post'] ); ?>"><?php _e( 'Completely Delete', $this->plugin_slug ); ?>
					</a>
				</div>
			<?php
		}
	}

	/**
	 * Completely delete admin action.
	 *
	 * @since  0.2
	 *
	 * @return void
	 */
	public function completely_delete() {

		if ( check_admin_referer() ) {
			$post_id = $_REQUEST['post'];

			add_action( 'wp_trash_post', array( $this, 'trash_post' ) );
			///////////////////////////////////////////////////////////////////////
			// Don't create revision when programically trash / delete posts, //
			// revisions will fail the untrash process                        //
			///////////////////////////////////////////////////////////////////////
			remove_action( 'post_updated', 'wp_save_post_revision' );

			wp_trash_post( $post_id );
			// remove_action( 'wp_trash_post', array( $this, 'trash_post' ) );

			$post_type = get_post_type( $post_id );
			$path = ( 'post' == $post_type ) ? '/edit.php' : '/edit.php?post_type=' . $post_type;
			wp_redirect( admin_url( $path ) );
		}
	}

	/**
	 * Trash all children posts.
	 *
	 * @since  0.2
	 *
	 * @param  int $post_id Post ID
	 * @return void
	 */
	public function trash_post( $post_id ) {

		global $wpdb;
		remove_action( 'post_updated', 'wp_save_post_revision' );

		// Get any related nav items, then trash them
		$items = $this->get_menu_items_by_post_id( $post_id );
		if ( $items ) {
			foreach ( $items as $item ) {
				$post = get_post( $item->post_id );
				if ( 'trash' != $post->post_status )
					wp_trash_post( $item->post_id );
			}
		}

		// Point children of this page to its parent, also clean the cache of affected children
		$options = $this->get_options();

		$sql = "SELECT ID, post_status FROM $wpdb->posts WHERE post_parent = %d AND post_type != 'revision'";
		$sql .= ( 'off' == $options['trash_attachments'] || ! isset( $options['trash_attachments'] ) ) ? " AND post_type != 'attachment'" : '';
		$children_query = $wpdb->prepare( $sql, $post_id );
		$children = $wpdb->get_results( $children_query );

		if ( $children ) {
			foreach ( $children as $child ) {
				if ( 'trash' != $child->post_status )
					wp_trash_post( $child->ID );
			}
		}
	}

	/**
	 * Delete attachments before deleting a post.
	 *
	 * @since 0.3
	 *
	 * @param  int $post_id Post ID
	 * @return void
	 */
	public function before_delete_post( $post_id ) {

		global $wpdb;

		// Get any related nav items, then delete them
		$items = $this->get_menu_items_by_post_id( $post_id );
		if ( $items ) {
			foreach ( $items as $item ) {
				$post = get_post( $item->post_id );
				if ( 'trash' == $post->post_status )
					wp_delete_post( $item->post_id, true );
			}
		}

		$children_query = $wpdb->prepare( "SELECT ID, post_status FROM $wpdb->posts WHERE post_parent = %d", $post_id );
		$children = $wpdb->get_results( $children_query );

		if ( $children ) {
			foreach ( $children as $child ) {
				if ( 'trash' == $child->post_status )
					wp_delete_post( $child->ID, true );
			}
		}
	}

	/**
	 * Untrash trashed posts.
	 *
	 * @since  0.2
	 *
	 * @param  int $post_id Post ID
	 * @return void
	 */
	public function untrash_post( $post_id ) {

		global $wpdb;
		remove_action( 'post_updated', 'wp_save_post_revision' );

		// Get any related nav items, then untrash them
		$items = $this->get_menu_items_by_post_id( $post_id );
		if ( $items ) {
			foreach ( $items as $item ) {
				$post = get_post( $item->post_id );
				if ( 'trash' == $post->post_status )
					wp_untrash_post( $item->post_id );
			}
		}

		// Point children of this page to its parent, also clean the cache of affected children
		$options = $this->get_options();

		$sql = "SELECT ID, post_status FROM $wpdb->posts WHERE post_parent = %d AND post_type != 'revision'";
		$sql .= ( 'off' == $options['trash_attachments'] || ! isset( $options['trash_attachments'] ) ) ? " AND post_type != 'attachment'" : '';
		$children_query = $wpdb->prepare( $sql, $post_id );
		$children = $wpdb->get_results( $children_query );

		if ( $children ) {
			foreach ( $children as $child ) {
				if ( 'trash' == $child->post_status )
					wp_untrash_post( $child->ID );
			}
		}
	}

	/**
	 * Add completely delete link into post row actions.
	 *
	 * @since    0.3
	 *
	 * @return array<string> Actions
	 */
	public function row_actions( $actions, $post ) {

		$post_type_object = get_post_type_object( $post->post_type );

		if ( 'trash' != $post->post_status && current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {
			$actions[$this->plugin_slug] = '<a class="submitcd delete" href="' . $this->get_action_url( $post->ID ) . '">' . __( 'Completely Delete', $this->plugin_slug ) . '</a>';
		}

		return $actions;
	}

	/**
	 * Get plugin options
	 *
	 * @since 0.5
	 *
	 * @return array<string> The setting options
	 */
	public function get_options() {

		$this->options = get_option( $this->plugin_slug );

		if ( empty( $this->options ) ) {
			$this->options = array(
				'trash_attachments' => 'off',
				'delete_attachments' => 'off',
			);
		}

		return $this->options;
	}

	/**
	 * Get menu items by post_id
	 *
	 * @param  int $post_id Post ID
	 * @return boolean|array An array of post id, false if the result is empty
	 */
	public function get_menu_items_by_post_id( $post_id ) {

		global $wpdb;
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %d", '_menu_item_object_id', $post_id ) );
		if ( ! empty( $results ) )
			return $results;

		return false;
	}

}
