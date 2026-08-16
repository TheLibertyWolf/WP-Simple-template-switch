<?php

if (!defined('ABSPATH')) {
    exit;
}

final class WP_Simple_Template_Switch
{
    private static bool $booted = false;

    public static function boot(): void
    {
        if (self::$booted) {
            return;
        }
        self::$booted = true;

        add_action('init', [self::class, 'load_textdomain']);
        add_filter('stylesheet', [self::class, 'filter_stylesheet'], 99);
        add_filter('template', [self::class, 'filter_template'], 99);

        add_action('show_user_profile', [self::class, 'render_profile_selector']);
        add_action('edit_user_profile', [self::class, 'render_profile_selector']);
        add_action('personal_options_update', [self::class, 'save_profile_selector']);
        add_action('edit_user_profile_update', [self::class, 'save_profile_selector']);

        add_action('admin_bar_menu', [self::class, 'render_admin_bar_switcher'], 90);
        add_action('admin_post_wp_simple_template_switch', [self::class, 'handle_switch']);
    }

    public static function activate(): void
    {
        if (get_option(WP_Simple_Template_Switch_Access::OPTION_NAME, null) === null) {
            $defaults = WP_Simple_Template_Switch_Access::defaults();
            $defaults['allowed_themes'] = [(string) get_option('stylesheet')];
            add_option(
                WP_Simple_Template_Switch_Access::OPTION_NAME,
                $defaults,
                '',
                false
            );
        }

        update_option('wp_simple_template_switch_version', WP_SIMPLE_TEMPLATE_SWITCH_VERSION, false);

        $administrator = get_role('administrator');
        if ($administrator instanceof WP_Role) {
            $administrator->add_cap(WP_Simple_Template_Switch_Access::MANAGE_CAPABILITY);
        }
    }

    public static function maybe_upgrade(): void
    {
        $installed_version = (string) get_option('wp_simple_template_switch_version', '1.1.0');
        if (version_compare($installed_version, WP_SIMPLE_TEMPLATE_SWITCH_VERSION, '>=')) {
            return;
        }

        $settings = get_option(WP_Simple_Template_Switch_Access::OPTION_NAME, null);
        if (is_array($settings) && !array_key_exists('allowed_themes', $settings)) {
            $settings['allowed_themes'] = [(string) get_option('stylesheet')];
            update_option(WP_Simple_Template_Switch_Access::OPTION_NAME, $settings, false);
        }

        update_option('wp_simple_template_switch_version', WP_SIMPLE_TEMPLATE_SWITCH_VERSION, false);
    }

    public static function load_textdomain(): void
    {
        load_plugin_textdomain(
            'wp-simple-template-switch',
            false,
            dirname(plugin_basename(WP_SIMPLE_TEMPLATE_SWITCH_FILE)) . '/languages'
        );
    }

    public static function filter_stylesheet(string $stylesheet): string
    {
        $theme = self::theme_for_request();
        return $theme instanceof WP_Theme ? $theme->get_stylesheet() : $stylesheet;
    }

    public static function filter_template(string $template): string
    {
        $theme = self::theme_for_request();
        return $theme instanceof WP_Theme ? $theme->get_template() : $template;
    }

    private static function theme_for_request(): ?WP_Theme
    {
        if (
            is_admin()
            || wp_doing_cron()
            || (defined('WP_CLI') && WP_CLI)
            || !WP_Simple_Template_Switch_Access::can_choose()
        ) {
            return null;
        }

        return WP_Simple_Template_Switch_Access::selected_theme();
    }

    public static function render_profile_selector(WP_User $profile_user): void
    {
        $current_user_id = get_current_user_id();
        $editing_self = $current_user_id === (int) $profile_user->ID;
        if (!$editing_self && !WP_Simple_Template_Switch_Access::can_manage($current_user_id)) {
            return;
        }

        $settings = WP_Simple_Template_Switch_Access::settings();
        if (empty($settings['show_profile']) || !WP_Simple_Template_Switch_Access::can_choose((int) $profile_user->ID)) {
            return;
        }

        $selected = get_user_meta($profile_user->ID, WP_Simple_Template_Switch_Access::USER_META_KEY, true);
        $selected = is_string($selected) ? $selected : '';
        $default_theme = self::site_default_theme();
        ?>
        <h2><?php esc_html_e('Thème personnel', 'wp-simple-template-switch'); ?></h2>
        <table class="form-table" role="presentation">
            <tr>
                <th><label for="wp-simple-template-switch-theme"><?php esc_html_e('Thème du site', 'wp-simple-template-switch'); ?></label></th>
                <td>
                    <?php wp_nonce_field('wp_simple_template_switch_profile_' . $profile_user->ID, 'wp_simple_template_switch_profile_nonce'); ?>
                    <select name="wp_simple_template_switch_theme" id="wp-simple-template-switch-theme">
                        <option value="" <?php selected($selected, ''); ?>>
                            <?php
                            echo esc_html(sprintf(
                                /* translators: %s: site default theme name. */
                                __('Thème par défaut du site (%s)', 'wp-simple-template-switch'),
                                $default_theme->get('Name')
                            ));
                            ?>
                        </option>
                        <?php foreach (WP_Simple_Template_Switch_Access::selectable_themes() as $stylesheet => $theme) : ?>
                            <option value="<?php echo esc_attr($stylesheet); ?>" <?php selected($selected, $stylesheet); ?>>
                                <?php echo esc_html($theme->get('Name')); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">
                        <?php esc_html_e('Ce choix modifie uniquement le thème que cet utilisateur voit sur le site public.', 'wp-simple-template-switch'); ?>
                    </p>
                </td>
            </tr>
        </table>
        <?php
    }

    public static function save_profile_selector(int $user_id): void
    {
        if (!current_user_can('edit_user', $user_id)) {
            return;
        }

        $current_user_id = get_current_user_id();
        if ($current_user_id !== $user_id && !WP_Simple_Template_Switch_Access::can_manage($current_user_id)) {
            return;
        }

        $nonce = isset($_POST['wp_simple_template_switch_profile_nonce'])
            ? sanitize_text_field(wp_unslash($_POST['wp_simple_template_switch_profile_nonce']))
            : '';
        if (!wp_verify_nonce($nonce, 'wp_simple_template_switch_profile_' . $user_id)) {
            return;
        }

        if (!WP_Simple_Template_Switch_Access::can_choose($user_id)) {
            return;
        }

        $stylesheet = isset($_POST['wp_simple_template_switch_theme'])
            ? sanitize_text_field(wp_unslash($_POST['wp_simple_template_switch_theme']))
            : '';

        WP_Simple_Template_Switch_Access::save_theme($user_id, $stylesheet);
    }

    public static function render_admin_bar_switcher(WP_Admin_Bar $admin_bar): void
    {
        if (!is_user_logged_in() || !WP_Simple_Template_Switch_Access::can_choose()) {
            return;
        }

        $settings = WP_Simple_Template_Switch_Access::settings();
        if (empty($settings['show_admin_bar'])) {
            return;
        }

        $selected_theme = WP_Simple_Template_Switch_Access::selected_theme();
        $default_theme = self::site_default_theme();
        $current_name = $selected_theme instanceof WP_Theme
            ? $selected_theme->get('Name')
            : sprintf(
                /* translators: %s: site default theme name. */
                __('Par défaut (%s)', 'wp-simple-template-switch'),
                $default_theme->get('Name')
            );

        $admin_bar->add_node([
            'id' => 'wp-simple-template-switch',
            'title' => esc_html(sprintf(
                /* translators: %s: current theme name. */
                __('Thème : %s', 'wp-simple-template-switch'),
                $current_name
            )),
            'href' => self::switch_url(''),
            'meta' => ['class' => 'wp-simple-template-switch'],
        ]);

        $admin_bar->add_node([
            'parent' => 'wp-simple-template-switch',
            'id' => 'wp-simple-template-switch-default',
            'title' => ($selected_theme === null ? '&#10003; ' : '') . esc_html(sprintf(
                /* translators: %s: site default theme name. */
                __('Thème du site (%s)', 'wp-simple-template-switch'),
                $default_theme->get('Name')
            )),
            'href' => self::switch_url(''),
        ]);

        foreach (WP_Simple_Template_Switch_Access::selectable_themes() as $stylesheet => $theme) {
            $is_selected = $selected_theme instanceof WP_Theme && $selected_theme->get_stylesheet() === $stylesheet;
            $admin_bar->add_node([
                'parent' => 'wp-simple-template-switch',
                'id' => 'wp-simple-template-switch-' . sanitize_html_class($stylesheet),
                'title' => ($is_selected ? '&#10003; ' : '') . esc_html($theme->get('Name')),
                'href' => self::switch_url($stylesheet),
            ]);
        }
    }

    private static function switch_url(string $stylesheet): string
    {
        $url = add_query_arg(
            [
                'action' => 'wp_simple_template_switch',
                'theme' => $stylesheet,
            ],
            admin_url('admin-post.php')
        );

        return wp_nonce_url($url, 'wp_simple_template_switch_' . $stylesheet);
    }

    private static function site_default_theme(): WP_Theme
    {
        remove_filter('stylesheet', [self::class, 'filter_stylesheet'], 99);
        $stylesheet = (string) get_option('stylesheet');
        add_filter('stylesheet', [self::class, 'filter_stylesheet'], 99);

        return wp_get_theme($stylesheet);
    }

    public static function handle_switch(): void
    {
        if (!is_user_logged_in() || !WP_Simple_Template_Switch_Access::can_choose()) {
            wp_die(
                esc_html__('Vous n’êtes pas autorisé à choisir un thème.', 'wp-simple-template-switch'),
                esc_html__('Accès refusé', 'wp-simple-template-switch'),
                ['response' => 403]
            );
        }

        $stylesheet = isset($_GET['theme']) ? sanitize_text_field(wp_unslash($_GET['theme'])) : '';
        check_admin_referer('wp_simple_template_switch_' . $stylesheet);

        if (!WP_Simple_Template_Switch_Access::save_theme(get_current_user_id(), $stylesheet)) {
            wp_die(
                esc_html__('Le thème demandé est indisponible.', 'wp-simple-template-switch'),
                esc_html__('Thème indisponible', 'wp-simple-template-switch'),
                ['response' => 400]
            );
        }

        wp_safe_redirect(wp_get_referer() ?: home_url('/'));
        exit;
    }
}
