<?php
/**
 * Plugin Name: WP Simple Template Switch
 * Plugin URI: https://github.com/TheLibertyWolf/WP-Simple-template-switch
 * Description: Permet aux utilisateurs autorisés de choisir leur thème WordPress depuis leur profil ou la barre d’administration.
 * Version: 1.1.0
 * Requires at least: 6.5
 * Requires PHP: 8.1
 * Author: SAS Jessy System
 * Author URI: https://jessysystem.com
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-simple-template-switch
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WP_SIMPLE_TEMPLATE_SWITCH_VERSION', '1.1.0');
define('WP_SIMPLE_TEMPLATE_SWITCH_FILE', __FILE__);
define('WP_SIMPLE_TEMPLATE_SWITCH_DIR', plugin_dir_path(__FILE__));

$wp_simple_template_switch_modules = [
    'includes/class-wp-simple-template-switch-access.php',
    'includes/class-wp-simple-template-switch.php',
    'includes/class-wp-simple-template-switch-admin.php',
];

foreach ($wp_simple_template_switch_modules as $wp_simple_template_switch_module) {
    $wp_simple_template_switch_path = WP_SIMPLE_TEMPLATE_SWITCH_DIR . $wp_simple_template_switch_module;

    if (!is_readable($wp_simple_template_switch_path)) {
        add_action('admin_notices', static function () use ($wp_simple_template_switch_module): void {
            if (current_user_can('activate_plugins')) {
                printf(
                    '<div class="notice notice-error"><p>%s</p></div>',
                    esc_html(sprintf(
                        /* translators: %s: missing plugin module. */
                        __('WP Simple Template Switch : module indisponible (%s). Réinstallez le plugin.', 'wp-simple-template-switch'),
                        $wp_simple_template_switch_module
                    ))
                );
            }
        });
        return;
    }

    require_once $wp_simple_template_switch_path;
}

unset($wp_simple_template_switch_modules, $wp_simple_template_switch_module, $wp_simple_template_switch_path);

register_activation_hook(__FILE__, ['WP_Simple_Template_Switch', 'activate']);
WP_Simple_Template_Switch::boot();
WP_Simple_Template_Switch_Admin::boot();
