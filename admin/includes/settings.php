<?php

class Completely_Delete_Settings {

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
	 * @since    0.8.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     0.8.0
	 */
	private function __construct() {

		$plugin = Completely_Delete::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		add_action( 'admin_init', array( $this, 'admin_init' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.8.0
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
	 * Registering the Sections, Fields, and Settings.
	 *
	 * This function is registered with the 'admin_init' hook.
	 */
	public function admin_init() {

		if ( false == get_option( $this->plugin_slug ) ) {
			add_option( $this->plugin_slug, $this->default_settings() );
		} // end if

		add_settings_section(
			'general',
			__( 'General', $this->plugin_slug ),
			'',
			$this->plugin_slug
		);

		add_settings_field(
			'trash_attachments',
			__( 'Trash Post Attachments', $this->plugin_slug ),
			array( $this, 'trash_attachments_callback' ),
			$this->plugin_slug,
			'general'
		);

		add_settings_field(
			'delete_attachments',
			__( 'Delete Post Attachments', $this->plugin_slug ),
			array( $this, 'delete_attachments_callback' ),
			$this->plugin_slug,
			'general'
		);

		register_setting(
			$this->plugin_slug,
			$this->plugin_slug
		);

	} // end admin_init

	/**
	 * Provides default values for the plugin settings.
	 *
	 * @return  array<string> Default settings
	 */
	public function default_settings() {

		$defaults = array(
			'trash_attachments' => 'on',
			'delete_attachments' => 'on',
		);

		return apply_filters( 'default_settings', $defaults );

	} // end default_settings

	public function trash_attachments_callback() {

		$options = get_option( $this->plugin_slug );
		$option  = isset( $options['trash_attachments'] ) ? $options['trash_attachments'] : '';

		$html  = '<input type="checkbox" id="trash_attachments" name="' . $this->plugin_slug . '[trash_attachments]" value="on"' . checked( 'on', $option, false ) . '/>';
		$html .= '<label for="trash_attachments">' . __( 'Trash all attachments when trashing a post.', $this->plugin_slug ) . '</label>';

		echo $html;

	} // end trash_attachments_callback

	public function delete_attachments_callback() {

		$options = get_option( $this->plugin_slug );
		$option  = ( isset( $options['delete_attachments'] ) ) ? $options['delete_attachments'] : '';

		$html  = '<input type="checkbox" id="delete_attachments" name="' . $this->plugin_slug . '[delete_attachments]" value="on"' . checked( 'on', $option, false ) . '/>';
		$html .= '<label for="delete_attachments">' . __( 'Delete all trashed attachments when deleting a trashed post.', $this->plugin_slug ) . '</label>';

		echo $html;

	} // end delete_attachments_callback
}

Completely_Delete_Settings::get_instance();
?>