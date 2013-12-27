<?php

if ( !class_exists( 'Completely_Delete_Settings' ) ):
	class Completely_Delete_Settings {

	public $settings_api;

	function __construct() {
		$this->settings_api = new WeDevs_Settings_API;

		$plugin = Completely_Delete::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	function admin_init() {

		//set the settings
		$this->settings_api->set_sections( $this->get_settings_sections() );
		$this->settings_api->set_fields( $this->get_settings_fields() );

		//initialize settings
		$this->settings_api->admin_init();
	}

	function get_settings_sections() {
		$sections = array(
			array(
				'id' => $this->plugin_slug,
				'title' => __( 'Settings', $this->plugin_slug )
			)
		);
		return $sections;
	}

	/**
	 * Returns all the settings fields
	 *
	 * @return array settings fields
	 */
	function get_settings_fields() {
		$settings_fields = array(
			$this->plugin_slug => array(
				array(
					'name' => 'delete_attachments',
					'label' => __( 'Delete Post Attachments', $this->plugin_slug ),
					'desc' => __( 'Delete all attachments when deleting a trashed post (any post type).', $this->plugin_slug ),
					'type' => 'checkbox'
				)
			)
		);

		return $settings_fields;
	}

}
endif;

global $settings;
$settings = new Completely_Delete_Settings();
