<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Completely_Delete
 * @author    1fixdotio <1fixdotio@gmail.com>
 * @license   GPL-2.0+
 * @link      http://1fix.io/completely-delete
 * @copyright 2013 1Fix
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once( plugin_dir_path( __FILE__ ) . 'public/class-completely-delete.php' );

$plugin = Completely_Delete::get_instance();
delete_option( $plugin->get_plugin_slug() );
delete_option( 'cd-display-activation-message' );
/**
 * @todo Delete options in whole network
 */