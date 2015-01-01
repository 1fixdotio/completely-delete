<?php
/**
 * Completely Delete.
 *
 * A plugin to let you completely delete all related objects of a post.
 *
 * @package   Completely_Delete
 * @author    1fixdotio <1fixdotio@gmail.com>
 * @license   GPL-2.0+
 * @link      http://1fix.io/completely-delete
 * @copyright 2013 1Fix.io
 *
 * @wordpress-plugin
 * Plugin Name:       Completely Delete
 * Plugin URI:        http://1fix.io/completely-delete
 * Description:       A plugin to let you completely delete all related objects of a post.
 * Version:           0.8.2
 * Author:            1fixdotio
 * Author URI:        http://1fix.io
 * Text Domain:       completely-delete
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/1fixdotio/completely-delete
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-completely-delete.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'Completely_Delete', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Completely_Delete', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Completely_Delete', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-completely-delete-admin.php' );
	add_action( 'plugins_loaded', array( 'Completely_Delete_Admin', 'get_instance' ) );
}
