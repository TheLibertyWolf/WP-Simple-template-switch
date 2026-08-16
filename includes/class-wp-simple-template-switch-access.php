<?php

if (!defined('ABSPATH')) {
    exit;
}

final class WP_Simple_Template_Switch_Access
{
    public const OPTION_NAME = 'wp_simple_template_switch_settings';
    public const USER_META_KEY = 'wp_simple_template_switch_theme';
    public const MANAGE_CAPABILITY = 'manage_wp_simple_template_switch';

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'enabled' => true,
            'audience_mode' => 'roles',
            'allowed_roles' => ['administrator'],
            'allowed_users' => [],
            'allowed_themes' => [],
            'manager_roles' => ['administrator'],
            'manager_users' => [],
            'show_profile' => true,
            'show_admin_bar' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function settings(): array
    {
        $stored = get_option(self::OPTION_NAME, []);
        if (!is_array($stored)) {
            $stored = [];
        }

        $settings = array_replace(self::defaults(), $stored);
        foreach (['allowed_roles', 'allowed_users', 'allowed_themes', 'manager_roles', 'manager_users'] as $key) {
            if (!is_array($settings[$key])) {
                $settings[$key] = [];
            }
        }

        return $settings;
    }

    public static function can_choose(int $user_id = 0): bool
    {
        $user_id = $user_id > 0 ? $user_id : get_current_user_id();
        if ($user_id <= 0) {
            return false;
        }

        $settings = self::settings();
        if (empty($settings['enabled'])) {
            return false;
        }

        $user = get_userdata($user_id);
        if (!$user instanceof WP_User) {
            return false;
        }

        switch ($settings['audience_mode']) {
            case 'roles':
                return (bool) array_intersect($user->roles, array_map('strval', $settings['allowed_roles']));

            case 'users':
                return in_array($user_id, array_map('intval', $settings['allowed_users']), true);

            case 'all':
            default:
                return true;
        }
    }

    public static function can_manage(int $user_id = 0): bool
    {
        $user_id = $user_id > 0 ? $user_id : get_current_user_id();
        if ($user_id <= 0) {
            return false;
        }

        if (is_multisite() && is_super_admin($user_id)) {
            return true;
        }

        if (user_can($user_id, self::MANAGE_CAPABILITY)) {
            return true;
        }

        $user = get_userdata($user_id);
        if (!$user instanceof WP_User) {
            return false;
        }

        $settings = self::settings();
        $allowed_by_role = (bool) array_intersect($user->roles, array_map('strval', $settings['manager_roles']));
        $allowed_by_user = in_array($user_id, array_map('intval', $settings['manager_users']), true);

        return $allowed_by_role || $allowed_by_user;
    }

    /**
     * @return array<string, WP_Theme>
     */
    public static function themes(): array
    {
        $themes = wp_get_themes(['errors' => false]);

        uasort($themes, static function (WP_Theme $left, WP_Theme $right): int {
            return strcasecmp($left->get('Name'), $right->get('Name'));
        });

        return $themes;
    }

    /**
     * @return array<string, WP_Theme>
     */
    public static function selectable_themes(): array
    {
        $themes = self::themes();
        $allowed = array_fill_keys(array_map('strval', self::settings()['allowed_themes']), true);

        return array_intersect_key($themes, $allowed);
    }

    public static function theme_preference(int $user_id = 0): string
    {
        $user_id = $user_id > 0 ? $user_id : get_current_user_id();
        if ($user_id <= 0) {
            return '';
        }

        $stylesheet = get_user_meta($user_id, self::USER_META_KEY, true);
        return is_string($stylesheet) ? $stylesheet : '';
    }

    public static function selected_theme(int $user_id = 0): ?WP_Theme
    {
        $user_id = $user_id > 0 ? $user_id : get_current_user_id();
        if ($user_id <= 0) {
            return null;
        }

        $stylesheet = self::theme_preference($user_id);
        if ($stylesheet === '') {
            return null;
        }

        $themes = self::selectable_themes();
        return isset($themes[$stylesheet]) ? $themes[$stylesheet] : null;
    }

    public static function save_theme(int $user_id, string $stylesheet): bool
    {
        if ($stylesheet === '') {
            delete_user_meta($user_id, self::USER_META_KEY);
            return true;
        }

        $themes = self::selectable_themes();
        if (!isset($themes[$stylesheet])) {
            return false;
        }

        update_user_meta($user_id, self::USER_META_KEY, $stylesheet);
        return true;
    }
}
