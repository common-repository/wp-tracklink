<?php
/**
 * Uninstall
 *
 * Clean up the Database and Settings
 */

global $wpdb;
$table = $wpdb->prefix. "tracklink";
if(!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {
	exit();
}

        delete_option('tl_db_version');
        delete_option('tl_settings');

        $wpdb->query("DROP TABLE IF EXISTS $table");
?>
