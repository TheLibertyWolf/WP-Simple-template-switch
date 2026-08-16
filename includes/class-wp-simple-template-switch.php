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

        add_action('wp_enqueue_scripts', [self::class, 'enqueue_admin_bar_assets']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_admin_bar_assets']);
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
        if (is_array($settings)) {
            $settings_changed = false;

            if (!array_key_exists('allowed_themes', $settings)) {
                $settings['allowed_themes'] = [(string) get_option('stylesheet')];
                $settings_changed = true;
            }

            if (!array_key_exists('admin_bar_mode', $settings)) {
                $settings['admin_bar_mode'] = 'selector';
                $settings_changed = true;
            }

            if ($settings_changed) {
                update_option(WP_Simple_Template_Switch_Access::OPTION_NAME, $settings, false);
            }
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

    public static function enqueue_admin_bar_assets(): void
    {
        if (!is_admin_bar_showing() || !WP_Simple_Template_Switch_Access::can_choose()) {
            return;
        }

        $settings = WP_Simple_Template_Switch_Access::settings();
        if (empty($settings['show_admin_bar']) || $settings['admin_bar_mode'] !== 'selector') {
            return;
        }

        wp_enqueue_style(
            'wp-simple-template-switch-admin-bar',
            plugins_url('assets/admin-bar.css', WP_SIMPLE_TEMPLATE_SWITCH_FILE),
            [],
            WP_SIMPLE_TEMPLATE_SWITCH_VERSION
        );
        wp_enqueue_script(
            'wp-simple-template-switch-admin-bar',
            plugins_url('assets/admin-bar.js', WP_SIMPLE_TEMPLATE_SWITCH_FILE),
            [],
            WP_SIMPLE_TEMPLATE_SWITCH_VERSION,
            true
        );
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
        $default_stylesheet = $default_theme->get_stylesheet();
        $selected_stylesheet = $selected_theme instanceof WP_Theme ? $selected_theme->get_stylesheet() : '';
        $toggle_stylesheet = '';

        if ($selected_stylesheet === '' || $selected_stylesheet === $default_stylesheet) {
            foreach (WP_Simple_Template_Switch_Access::selectable_themes() as $stylesheet => $theme) {
                if ($stylesheet !== $default_stylesheet) {
                    $toggle_stylesheet = $stylesheet;
                    break;
                }
            }
        }

        $current_name = $selected_theme instanceof WP_Theme
            ? $selected_theme->get('Name')
            : sprintf(
                /* translators: %s: site default theme name. */
                __('Par défaut (%s)', 'wp-simple-template-switch'),
                $default_theme->get('Name')
            );

        $admin_bar_mode = $settings['admin_bar_mode'] === 'switch' ? 'switch' : 'selector';
        if ($admin_bar_mode === 'selector') {
            $admin_bar->add_node([
                'id' => 'wp-simple-template-switch',
                'title' => self::admin_bar_selector($selected_stylesheet, $default_theme),
                'href' => false,
                'meta' => ['class' => 'wp-simple-template-switch wpsts-selector-mode'],
            ]);
            return;
        }

        $admin_bar->add_node([
            'id' => 'wp-simple-template-switch',
            'title' => esc_html(sprintf(
                /* translators: %s: current theme name. */
                __('Thème : %s', 'wp-simple-template-switch'),
                $current_name
            )),
            'href' => self::switch_url($toggle_stylesheet),
            'meta' => ['class' => 'wp-simple-template-switch wpsts-switch-mode'],
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

    private static function admin_bar_selector(string $selected_stylesheet, WP_Theme $default_theme): string
    {
        $default_stylesheet = $default_theme->get_stylesheet();
        $default_selected = $selected_stylesheet === '' || $selected_stylesheet === $default_stylesheet;
        $options = sprintf(
            '<option value="%s"%s>%s</option>',
            esc_url(self::switch_url('')),
            selected($default_selected, true, false),
            esc_html(sprintf(
                /* translators: %s: site default theme name. */
                __('Thème du site (%s)', 'wp-simple-template-switch'),
                $default_theme->get('Name')
            ))
        );

        foreach (WP_Simple_Template_Switch_Access::selectable_themes() as $stylesheet => $theme) {
            if ($stylesheet === $default_stylesheet) {
                continue;
            }

            $options .= sprintf(
                '<option value="%s"%s>%s</option>',
                esc_url(self::switch_url($stylesheet)),
                selected($selected_stylesheet, $stylesheet, false),
                esc_html($theme->get('Name'))
            );
        }

        return sprintf(
            '<select class="wpsts-admin-bar-select" aria-label="%s">%s</select>',
            esc_attr__('Choisir un thème', 'wp-simple-template-switch'),
            $options
        );
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

        $redirect = wp_get_referer() ?: home_url('/');
        $redirect = wp_validate_redirect($redirect, home_url('/'));

        if (str_starts_with($redirect, admin_url())) {
            $redirect = home_url('/');
        }

        wp_safe_redirect($redirect);
        exit;
    }
}
