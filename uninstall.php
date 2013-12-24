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

// @TODO: Define uninstall functionality here