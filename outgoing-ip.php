<?php

/**
 * Plugin Name: Outgoing IP
 * Plugin URI: https://rue-de-la-vieille.fr
 * Author: Jérôme Mulsant
 * Author URI: https://rue-de-la-vieille.fr
 * Description: Display outgoing IP of current website
 * Text Domain: outgoing-ip
 * Domain Path: /languages
 * Version: GIT
 */

add_action('admin_menu', function () {
    add_submenu_page(
        'tools.php',
        __('IP Sortante', 'outgoing-ip'),
        __('IP Sortante', 'outgoing-ip'),
        'manage_options',
        'outgoing-ip',
        function () {
            echo '<div class="wrap">';
            printf('<h1>%s</h1>', __('IP Sortante', 'outgoing-ip'));
            $sources = [
                'IPv4' => apply_filters('outgoing_ipv4_url', 'https://ip.rue-de-la-vieille.fr'),
                'IPv6' => apply_filters('outgoing_ipv6_url', 'https://ip6.rue-de-la-vieille.fr'),
            ];
            printf(
                '<table class="widefat striped">%s<tbody>%s</tbody></table>',
                sprintf(
                    '<thead><tr><th>%s</th><th>%s</th><th>%s</th></tr></thead>',
                    __('Protocole', 'outgoing-ip'),
                    __('Adresse', 'outgoing-ip'),
                    __('Source', 'outgoing-ip'),
                ),
                implode('', array_map(function ($label, $url) {
                    $response = wp_remote_get($url);
                    return sprintf(
                        '<tr><th scope="row">%s</th><td>%s</td><td><a href="%s">%s</a></td></tr>',
                        $label,
                        is_wp_error($response)
                            ? sprintf(__('Erreur : %s', 'outgoing-ip'), $response->get_error_message())
                            : $response['body'],
                        $url,
                        parse_url($url, PHP_URL_HOST)
                    );
                }, array_keys($sources), $sources))
            );
            echo '</div>';
        }
    );
});

add_action('plugins_loaded', function () {
    $textdomain = 'outgoing-ip';

    /** This filter is documented in wp-includes/l10n.php */
    $locale = apply_filters('plugin_locale', determine_locale(), $textdomain);
    $mofile = $textdomain . '-' . $locale . '.mo';

    // Load from plugin languages folder.
    load_textdomain($textdomain, __DIR__ . '/languages/' . $mofile);

    // Override with public languages directory.
    load_textdomain($textdomain, WP_LANG_DIR . '/plugins/' . $mofile);
});
