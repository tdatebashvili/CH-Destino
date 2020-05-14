<?php
/**
 * Ch-Destino Uninstall
 *
 * Uninstalls the plugin deletes user roles, tables, and options.
 *
*/
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
global $wpdb;

// Borra options.
$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'ch_destino%';" );

// Borra usermeta.
$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key LIKE 'ch_destino%';" );

// Elimina la tabla del la base de datos.

$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}ch_destino");

// Limpia la cache.
wp_cache_flush();
