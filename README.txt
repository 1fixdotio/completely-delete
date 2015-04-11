=== Completely Delete ===

Contributors: 1fixdotio, yoren
Donate link: http://1fix.io/
Tags: posts, pages, attachemnts
Requires at least: 3.5
Tested up to: 4.1
Stable tag: 0.8.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin to let you completely delete all child objects of a post.

== Description ==

This plugin is made for:

* Trash / Delete all children posts of a post, if the post type is hierarchical.
* Restore all children posts of a trashed post, if the post type is hierarchical.
* Trash / Delete nav items.
* Optional: Trash all attachments when trash a post. Enable this feature on plugin settings page.
* Optional: Delete all attachments when deleting a trashed post. Enable this feature on plugin settings page.
* TODO - Optional: Delete all terms a post belongs to, if those terms are empty. Enable this feature on plugin settings page.
* TODO - Select which roles are allowed to perform this action on plugin settings page.

== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'completely-delete'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `completely-delete.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `completely-delete.zip`
2. Extract the `completely-delete` directory to your computer
3. Upload the `completely-delete` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard

== Screenshots ==

1. A "Completely Delete" link will be added to the row actions
2. A "Completely Delete" link will be added to the left of Update button
3. A settings page for this plugin

== Changelog ==

= 0.8.3 =
* Fix minor bugs when trash/untrash posts.

= 0.8.2 =
* Fix a minor bug when untrash posts.

= 0.8.1 =
* Display Completely Delete link only when the user with the delete post capability.

= 0.8.0 =
* Use 3-digit version number.
* Enable the "trash / delete attachments with a post" options by default.
* Remove weDevs Settings API wrapper class. Use WordPress Settings API directly.
* Debug and improve performance via Scrutinizer.

= 0.7 =
* Display an admin notice when plugin activated.
* Add uninstall functions.

= 0.6 =
* Remove assets folder, which should not be included.
* Translation for Traditional Chinese has been added.

= 0.5.6 =
* The first version
