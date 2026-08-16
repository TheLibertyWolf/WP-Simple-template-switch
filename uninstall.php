<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('wp_simple_template_switch_settings');
delete_option('wp_simple_template_switch_version');

if (is_multisite()) {
    delete_site_option('wp_simple_template_switch_settings');
}

global $wpdb;
$wpdb->delete(
    $wpdb->usermeta,
    ['meta_key' => 'wp_simple_template_switch_theme'],
    ['%s']
);

foreach (wp_roles()->roles as $role_name => $details) {
    $role = get_role($role_name);
    if ($role instanceof WP_Role) {
        $role->remove_cap('manage_wp_simple_template_switch');
    }
}
