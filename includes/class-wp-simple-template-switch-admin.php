<?php

if (!defined('ABSPATH')) {
    exit;
}

final class WP_Simple_Template_Switch_Admin
{
    private static string $page_hook = '';

    public static function boot(): void
    {
        add_action('admin_menu', [self::class, 'register_menu']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_assets']);
        add_action('admin_post_wp_simple_template_switch_save_settings', [self::class, 'save_settings']);
        add_filter('plugin_action_links_' . plugin_basename(WP_SIMPLE_TEMPLATE_SWITCH_FILE), [self::class, 'action_links']);
    }

    public static function register_menu(): void
    {
        if (!WP_Simple_Template_Switch_Access::can_manage()) {
            return;
        }

        self::$page_hook = (string) add_options_page(
            __('WP Simple Template Switch', 'wp-simple-template-switch'),
            __('Template Switch', 'wp-simple-template-switch'),
            'read',
            'wp-simple-template-switch',
            [self::class, 'render_page']
        );
    }

    public static function enqueue_assets(string $hook): void
    {
        if ($hook !== self::$page_hook) {
            return;
        }

        wp_enqueue_style(
            'wp-simple-template-switch-admin',
            plugins_url('assets/admin.css', WP_SIMPLE_TEMPLATE_SWITCH_FILE),
            [],
            WP_SIMPLE_TEMPLATE_SWITCH_VERSION
        );
    }

    /**
     * @param array<int, string> $links
     * @return array<int, string>
     */
    public static function action_links(array $links): array
    {
        if (WP_Simple_Template_Switch_Access::can_manage()) {
            array_unshift(
                $links,
                '<a href="' . esc_url(admin_url('options-general.php?page=wp-simple-template-switch')) . '">' .
                esc_html__('Réglages', 'wp-simple-template-switch') . '</a>'
            );
        }

        return $links;
    }

    public static function render_page(): void
    {
        if (!WP_Simple_Template_Switch_Access::can_manage()) {
            wp_die(
                esc_html__('Vous n’êtes pas autorisé à gérer ces réglages.', 'wp-simple-template-switch'),
                esc_html__('Accès refusé', 'wp-simple-template-switch'),
                ['response' => 403]
            );
        }

        $settings = WP_Simple_Template_Switch_Access::settings();
        $roles = wp_roles()->roles;
        $users = get_users([
            'orderby' => 'display_name',
            'order' => 'ASC',
            'fields' => 'all_with_meta',
        ]);
        ?>
        <div class="wrap wp-simple-template-switch-settings">
            <h1><?php esc_html_e('WP Simple Template Switch', 'wp-simple-template-switch'); ?></h1>
            <p class="description">
                <?php esc_html_e('Autorisez des utilisateurs à tester un autre thème sans modifier le thème actif pour les autres visiteurs.', 'wp-simple-template-switch'); ?>
            </p>

            <?php if (isset($_GET['settings-updated'])) : ?>
                <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Réglages enregistrés.', 'wp-simple-template-switch'); ?></p></div>
            <?php endif; ?>

            <div id="poststuff" class="wpsts-poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="wp_simple_template_switch_save_settings">
                <?php wp_nonce_field('wp_simple_template_switch_save_settings'); ?>

                <section class="wpsts-card">
                    <h2><?php esc_html_e('Activation et emplacements', 'wp-simple-template-switch'); ?></h2>
                    <label class="wpsts-toggle">
                        <input type="checkbox" name="enabled" value="1" <?php checked(!empty($settings['enabled'])); ?>>
                        <span><?php esc_html_e('Activer le choix d’un thème personnel', 'wp-simple-template-switch'); ?></span>
                    </label>
                    <label class="wpsts-toggle">
                        <input type="checkbox" name="show_profile" value="1" <?php checked(!empty($settings['show_profile'])); ?>>
                        <span><?php esc_html_e('Afficher le sélecteur sur la page de profil', 'wp-simple-template-switch'); ?></span>
                    </label>
                    <label class="wpsts-toggle">
                        <input type="checkbox" name="show_admin_bar" value="1" <?php checked(!empty($settings['show_admin_bar'])); ?>>
                        <span><?php esc_html_e('Afficher le sélecteur dans la barre d’administration', 'wp-simple-template-switch'); ?></span>
                    </label>
                </section>

                <section class="wpsts-card">
                    <h2><?php esc_html_e('Qui peut choisir son thème ?', 'wp-simple-template-switch'); ?></h2>
                    <fieldset class="wpsts-choice-list">
                        <label><input type="radio" name="audience_mode" value="all" <?php checked($settings['audience_mode'], 'all'); ?>> <?php esc_html_e('Tous les utilisateurs connectés', 'wp-simple-template-switch'); ?></label>
                        <label><input type="radio" name="audience_mode" value="roles" <?php checked($settings['audience_mode'], 'roles'); ?>> <?php esc_html_e('Uniquement les rôles sélectionnés', 'wp-simple-template-switch'); ?></label>
                        <label><input type="radio" name="audience_mode" value="users" <?php checked($settings['audience_mode'], 'users'); ?>> <?php esc_html_e('Uniquement les utilisateurs sélectionnés', 'wp-simple-template-switch'); ?></label>
                    </fieldset>

                    <div class="wpsts-columns">
                        <div>
                            <h3><?php esc_html_e('Rôles autorisés', 'wp-simple-template-switch'); ?></h3>
                            <div class="wpsts-scrollbox">
                                <?php self::render_role_checkboxes('allowed_roles', $roles, $settings['allowed_roles']); ?>
                            </div>
                        </div>
                        <div>
                            <h3><?php esc_html_e('Utilisateurs autorisés', 'wp-simple-template-switch'); ?></h3>
                            <div class="wpsts-scrollbox">
                                <?php self::render_user_checkboxes('allowed_users', $users, $settings['allowed_users']); ?>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="wpsts-card">
                    <h2><?php esc_html_e('Qui peut accéder à ces réglages ?', 'wp-simple-template-switch'); ?></h2>
                    <p><?php esc_html_e('L’accès peut être accordé par rôle et/ou individuellement. Les super-administrateurs Multisite conservent toujours l’accès.', 'wp-simple-template-switch'); ?></p>
                    <div class="wpsts-columns">
                        <div>
                            <h3><?php esc_html_e('Rôles gestionnaires', 'wp-simple-template-switch'); ?></h3>
                            <div class="wpsts-scrollbox">
                                <?php self::render_role_checkboxes('manager_roles', $roles, $settings['manager_roles']); ?>
                            </div>
                        </div>
                        <div>
                            <h3><?php esc_html_e('Utilisateurs gestionnaires', 'wp-simple-template-switch'); ?></h3>
                            <div class="wpsts-scrollbox">
                                <?php self::render_user_checkboxes('manager_users', $users, $settings['manager_users']); ?>
                            </div>
                        </div>
                    </div>
                    <p class="description"><?php esc_html_e('Si aucune entrée n’est cochée, votre propre compte sera conservé comme gestionnaire afin d’éviter un verrouillage.', 'wp-simple-template-switch'); ?></p>
                </section>

                <section class="wpsts-card">
                    <h2><?php esc_html_e('Thèmes disponibles dans le switch', 'wp-simple-template-switch'); ?></h2>
                    <p><?php esc_html_e('Cochez uniquement les thèmes que les utilisateurs autorisés peuvent sélectionner.', 'wp-simple-template-switch'); ?></p>
                    <div class="wpsts-theme-grid">
                        <?php foreach (WP_Simple_Template_Switch_Access::themes() as $stylesheet => $theme) : ?>
                            <label class="wpsts-theme-option">
                                <input type="checkbox" name="allowed_themes[]" value="<?php echo esc_attr($stylesheet); ?>" <?php checked(in_array($stylesheet, $settings['allowed_themes'], true)); ?>>
                                <span>
                                    <strong><?php echo esc_html($theme->get('Name')); ?></strong>
                                    <code><?php echo esc_html($stylesheet); ?></code>
                                    <small>
                                        <?php echo esc_html(sprintf(__('Version %s', 'wp-simple-template-switch'), $theme->get('Version'))); ?>
                                        <?php if ($stylesheet === (string) get_option('stylesheet')) : ?>
                                            — <?php esc_html_e('thème actif du site', 'wp-simple-template-switch'); ?>
                                        <?php endif; ?>
                                    </small>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="description"><?php esc_html_e('Les thèmes non cochés restent installés, mais sont absents du profil et de la barre d’administration. Le thème par défaut du site reste toujours accessible.', 'wp-simple-template-switch'); ?></p>
                </section>

                <section class="wpsts-card">
                    <h2><?php esc_html_e('Utilisation des thèmes', 'wp-simple-template-switch'); ?></h2>
                    <p><?php esc_html_e('Consultez le thème utilisé par chaque compte et repérez les préférences qui ne sont plus applicables.', 'wp-simple-template-switch'); ?></p>
                    <?php self::render_theme_usage($users); ?>
                </section>

                <?php submit_button(__('Enregistrer les réglages', 'wp-simple-template-switch')); ?>
            </form>

                    </div>

                    <div id="postbox-container-1" class="postbox-container">
                        <div class="postbox">
                            <div class="postbox-header"><h2 class="hndle"><?php esc_html_e('État', 'wp-simple-template-switch'); ?></h2></div>
                            <div class="inside">
                                <p class="wpsts-status <?php echo !empty($settings['enabled']) ? 'wpsts-status-active' : 'wpsts-status-inactive'; ?>">
                                    <span class="dashicons <?php echo !empty($settings['enabled']) ? 'dashicons-yes-alt' : 'dashicons-minus'; ?>" aria-hidden="true"></span>
                                    <strong><?php echo esc_html(!empty($settings['enabled']) ? __('Choix personnel actif', 'wp-simple-template-switch') : __('Choix personnel désactivé', 'wp-simple-template-switch')); ?></strong>
                                </p>
                                <p>
                                    <?php
                                    echo esc_html(sprintf(
                                        /* translators: %d: number of installed themes. */
                                        _n('%d thème détecté', '%d thèmes détectés', count(WP_Simple_Template_Switch_Access::themes()), 'wp-simple-template-switch'),
                                        count(WP_Simple_Template_Switch_Access::themes())
                                    ));
                                    ?>
                                </p>
                                <p class="description"><?php esc_html_e('Le thème actif du site n’est jamais modifié par les choix personnels.', 'wp-simple-template-switch'); ?></p>
                            </div>
                        </div>

                        <div class="postbox">
                            <div class="postbox-header"><h2 class="hndle"><?php esc_html_e('Utilisation', 'wp-simple-template-switch'); ?></h2></div>
                            <div class="inside">
                                <ol>
                                    <li><?php esc_html_e('Choisissez les utilisateurs ou rôles autorisés.', 'wp-simple-template-switch'); ?></li>
                                    <li><?php esc_html_e('Installez le thème à tester sans l’activer globalement.', 'wp-simple-template-switch'); ?></li>
                                    <li><?php esc_html_e('Basculez depuis le profil ou la barre d’administration.', 'wp-simple-template-switch'); ?></li>
                                </ol>
                                <p class="description"><?php esc_html_e('Chaque préférence est enregistrée dans le profil WordPress de l’utilisateur.', 'wp-simple-template-switch'); ?></p>
                            </div>
                        </div>

                        <div class="postbox">
                            <div class="postbox-header"><h2 class="hndle"><?php esc_html_e('Sécurité des accès', 'wp-simple-template-switch'); ?></h2></div>
                            <div class="inside">
                                <p><?php esc_html_e('Les changements sont protégés par les nonces WordPress et contrôlés côté serveur.', 'wp-simple-template-switch'); ?></p>
                                <p class="description"><?php esc_html_e('Le dernier gestionnaire ne peut pas être supprimé accidentellement : le compte qui enregistre est conservé si nécessaire.', 'wp-simple-template-switch'); ?></p>
                            </div>
                        </div>

                        <div class="postbox">
                            <div class="postbox-header"><h2 class="hndle"><?php esc_html_e('Aptitude avancée', 'wp-simple-template-switch'); ?></h2></div>
                            <div class="inside">
                                <p><?php esc_html_e('Pour déléguer l’accès avec un gestionnaire de rôles ou d’aptitudes, attribuez :', 'wp-simple-template-switch'); ?></p>
                                <p><code>manage_wp_simple_template_switch</code></p>
                                <p class="description"><?php esc_html_e('Cette aptitude est attribuée automatiquement au rôle Administrateur lors de l’activation. Elle s’ajoute aux rôles et utilisateurs sélectionnés dans cette page.', 'wp-simple-template-switch'); ?></p>
                            </div>
                        </div>

                        <div class="postbox">
                            <div class="postbox-header"><h2 class="hndle"><?php esc_html_e('Auteur', 'wp-simple-template-switch'); ?></h2></div>
                            <div class="inside">
                                <p><strong>SAS Jessy System</strong></p>
                                <p><a href="https://jessysystem.com" target="_blank" rel="noopener noreferrer">jessysystem.com</a></p>
                                <p>
                                    <a class="button wpsts-github-button" href="https://github.com/TheLibertyWolf/WP-Simple-template-switch" target="_blank" rel="noopener noreferrer">
                                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 .7a11.3 11.3 0 0 0-3.6 22c.6.1.8-.2.8-.5v-2c-3.3.7-4-1.4-4-1.4-.6-1.4-1.4-1.8-1.4-1.8-1.1-.8.1-.8.1-.8 1.2.1 1.9 1.3 1.9 1.3 1.1 1.9 2.9 1.3 3.5 1 .1-.8.4-1.3.8-1.6-2.7-.3-5.5-1.3-5.5-5.9 0-1.3.5-2.4 1.2-3.2-.1-.3-.5-1.6.1-3.2 0 0 1-.3 3.3 1.2a11.4 11.4 0 0 1 6 0c2.3-1.5 3.3-1.2 3.3-1.2.6 1.6.2 2.9.1 3.2.8.8 1.2 1.9 1.2 3.2 0 4.6-2.8 5.6-5.5 5.9.4.4.8 1.1.8 2.2v3.3c0 .3.2.6.8.5A11.3 11.3 0 0 0 12 .7Z"/></svg>
                                        <span><?php esc_html_e('Voir le projet sur GitHub', 'wp-simple-template-switch'); ?></span>
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <br class="clear">
            </div>
        </div>
        <?php
    }

    /**
     * @param array<string, array<string, mixed>> $roles
     * @param array<int, string>                  $selected_roles
     */
    private static function render_role_checkboxes(string $name, array $roles, array $selected_roles): void
    {
        foreach ($roles as $role_key => $role) {
            ?>
            <label>
                <input type="checkbox" name="<?php echo esc_attr($name); ?>[]" value="<?php echo esc_attr($role_key); ?>" <?php checked(in_array($role_key, $selected_roles, true)); ?>>
                <?php echo esc_html(translate_user_role($role['name'])); ?>
                <code><?php echo esc_html($role_key); ?></code>
            </label>
            <?php
        }
    }

    /**
     * @param array<int, object> $users
     * @param array<int, int>    $selected_users
     */
    private static function render_user_checkboxes(string $name, array $users, array $selected_users): void
    {
        $selected_users = array_map('intval', $selected_users);
        foreach ($users as $user) {
            ?>
            <label>
                <input type="checkbox" name="<?php echo esc_attr($name); ?>[]" value="<?php echo esc_attr((string) $user->ID); ?>" <?php checked(in_array((int) $user->ID, $selected_users, true)); ?>>
                <?php echo esc_html($user->display_name); ?>
                <code><?php echo esc_html($user->user_login); ?></code>
            </label>
            <?php
        }
    }

    /**
     * @param array<int, WP_User> $users
     */
    private static function render_theme_usage(array $users): void
    {
        $themes = WP_Simple_Template_Switch_Access::themes();
        $selectable = WP_Simple_Template_Switch_Access::selectable_themes();
        $default_stylesheet = (string) get_option('stylesheet');
        $default_theme = isset($themes[$default_stylesheet]) ? $themes[$default_stylesheet] : wp_get_theme($default_stylesheet);
        $roles = wp_roles()->roles;
        ?>
        <div class="wpsts-usage-table-wrap">
            <table class="widefat striped wpsts-usage-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Utilisateur', 'wp-simple-template-switch'); ?></th>
                        <th><?php esc_html_e('Rôle(s)', 'wp-simple-template-switch'); ?></th>
                        <th><?php esc_html_e('Thème', 'wp-simple-template-switch'); ?></th>
                        <th><?php esc_html_e('État', 'wp-simple-template-switch'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <?php
                        $preference = WP_Simple_Template_Switch_Access::theme_preference((int) $user->ID);
                        $theme_name = $default_theme->exists() ? $default_theme->get('Name') : $default_stylesheet;
                        $status = __('Par défaut', 'wp-simple-template-switch');
                        $status_class = 'wpsts-usage-default';

                        if ($preference !== '') {
                            if (!isset($themes[$preference])) {
                                $theme_name = $preference;
                                $status = __('Thème indisponible', 'wp-simple-template-switch');
                                $status_class = 'wpsts-usage-warning';
                            } elseif (!isset($selectable[$preference])) {
                                $theme_name = $themes[$preference]->get('Name');
                                $status = __('Non proposé', 'wp-simple-template-switch');
                                $status_class = 'wpsts-usage-warning';
                            } elseif (!WP_Simple_Template_Switch_Access::can_choose((int) $user->ID)) {
                                $theme_name = $themes[$preference]->get('Name');
                                $status = __('Accès retiré', 'wp-simple-template-switch');
                                $status_class = 'wpsts-usage-warning';
                            } else {
                                $theme_name = $themes[$preference]->get('Name');
                                $status = __('Actif', 'wp-simple-template-switch');
                                $status_class = 'wpsts-usage-active';
                            }
                        }

                        $role_names = [];
                        foreach ($user->roles as $role_key) {
                            if (isset($roles[$role_key]['name'])) {
                                $role_names[] = translate_user_role($roles[$role_key]['name']);
                            }
                        }
                        ?>
                        <tr>
                            <td><strong><?php echo esc_html($user->display_name); ?></strong><br><code><?php echo esc_html($user->user_login); ?></code></td>
                            <td><?php echo esc_html($role_names !== [] ? implode(', ', $role_names) : '—'); ?></td>
                            <td><?php echo esc_html($theme_name); ?></td>
                            <td><span class="wpsts-usage-status <?php echo esc_attr($status_class); ?>"><?php echo esc_html($status); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public static function save_settings(): void
    {
        if (!WP_Simple_Template_Switch_Access::can_manage()) {
            wp_die(
                esc_html__('Vous n’êtes pas autorisé à gérer ces réglages.', 'wp-simple-template-switch'),
                esc_html__('Accès refusé', 'wp-simple-template-switch'),
                ['response' => 403]
            );
        }

        check_admin_referer('wp_simple_template_switch_save_settings');

        $valid_roles = array_keys(wp_roles()->roles);
        $audience_mode = isset($_POST['audience_mode'])
            ? sanitize_key(wp_unslash($_POST['audience_mode']))
            : 'all';
        if (!in_array($audience_mode, ['all', 'roles', 'users'], true)) {
            $audience_mode = 'all';
        }

        $settings = [
            'enabled' => isset($_POST['enabled']),
            'audience_mode' => $audience_mode,
            'allowed_roles' => self::sanitize_roles($_POST['allowed_roles'] ?? [], $valid_roles),
            'allowed_users' => self::sanitize_users($_POST['allowed_users'] ?? []),
            'allowed_themes' => self::sanitize_themes($_POST['allowed_themes'] ?? []),
            'manager_roles' => self::sanitize_roles($_POST['manager_roles'] ?? [], $valid_roles),
            'manager_users' => self::sanitize_users($_POST['manager_users'] ?? []),
            'show_profile' => isset($_POST['show_profile']),
            'show_admin_bar' => isset($_POST['show_admin_bar']),
        ];

        if (
            $settings['manager_roles'] === []
            && $settings['manager_users'] === []
            && !current_user_can(WP_Simple_Template_Switch_Access::MANAGE_CAPABILITY)
        ) {
            $settings['manager_users'] = [get_current_user_id()];
        }

        update_option(WP_Simple_Template_Switch_Access::OPTION_NAME, $settings, false);

        wp_safe_redirect(add_query_arg(
            [
                'page' => 'wp-simple-template-switch',
                'settings-updated' => 'true',
            ],
            admin_url('options-general.php')
        ));
        exit;
    }

    /**
     * @param mixed              $raw_roles
     * @param array<int, string> $valid_roles
     * @return array<int, string>
     */
    private static function sanitize_roles($raw_roles, array $valid_roles): array
    {
        if (!is_array($raw_roles)) {
            return [];
        }

        $roles = array_map('sanitize_key', wp_unslash($raw_roles));
        return array_values(array_intersect(array_unique($roles), $valid_roles));
    }

    /**
     * @param mixed $raw_users
     * @return array<int, int>
     */
    private static function sanitize_users($raw_users): array
    {
        if (!is_array($raw_users)) {
            return [];
        }

        $user_ids = array_values(array_unique(array_filter(array_map('absint', wp_unslash($raw_users)))));
        if ($user_ids === []) {
            return [];
        }

        $existing = get_users([
            'include' => $user_ids,
            'fields' => 'ID',
        ]);

        return array_values(array_map('intval', $existing));
    }

    /**
     * @param mixed $raw_themes
     * @return array<int, string>
     */
    private static function sanitize_themes($raw_themes): array
    {
        if (!is_array($raw_themes)) {
            return [];
        }

        $requested = array_map(
            static fn ($theme): string => sanitize_text_field((string) $theme),
            wp_unslash($raw_themes)
        );
        $installed = array_keys(WP_Simple_Template_Switch_Access::themes());

        return array_values(array_intersect(array_unique($requested), $installed));
    }
}
