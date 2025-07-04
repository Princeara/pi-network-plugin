<?php
/**
 * Plugin Name: Pi Network WordPress Integration
 * Description: Integrates Pi Network login and payments with WordPress.
 * Version: 1.0
 * Author: Olatoye + Copilot
 * Text Domain: pi-network-plugin
 */

if (!defined('ABSPATH')) exit;

define('PI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PI_PLUGIN_URL', plugin_dir_url(__FILE__));

// Includes
require_once PI_PLUGIN_DIR . 'includes/pi-auth-handler.php';
require_once PI_PLUGIN_DIR . 'includes/pi-payment-verifier.php';
require_once PI_PLUGIN_DIR . 'includes/pi-payment-display.php';
require_once PI_PLUGIN_DIR . 'includes/pi-admin-widget.php';
require_once PI_PLUGIN_DIR . 'admin/settings-page.php';

// 🌐 Load translations
add_action('plugins_loaded', function () {
    load_plugin_textdomain('pi-network-plugin', false, dirname(plugin_basename(__FILE__)) . '/languages/');
});

// Settings helper
function pi_get_plugin_settings() {
    return [
        'app_id'        => get_option('pi_app_id'),
        'private_key'   => get_option('pi_private_key'),
        'amount'        => get_option('pi_payment_amount', 1),
        'memo'          => get_option('pi_payment_memo', 'Pi WP Payment'),
        'sandbox'       => get_option('pi_sandbox_mode'),
        'email_user'    => get_option('pi_email_user'),
        'email_admin'   => get_option('pi_email_admin')
    ];
}

// Assets
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('pi-sdk', 'https://sdk.minepi.com/pi-sdk.js', [], null, true);
    wp_enqueue_style('pi-style', PI_PLUGIN_URL . 'assets/pi-style.css', [], '1.0');
    wp_enqueue_script('pi-custom', PI_PLUGIN_URL . 'assets/pi-sdk-handler.js', ['pi-sdk'], '1.0', true);
    wp_localize_script('pi-custom', 'piAjax', [
        'ajax_url'       => admin_url('admin-ajax.php'),
        'app_id'         => get_option('pi_app_id'),
        'payment_amount' => get_option('pi_payment_amount', 1),
        'payment_memo'   => get_option('pi_payment_memo', 'Pi WP Payment'),
        'sandbox_mode'   => get_option('pi_sandbox_mode') ? true : false
    ]);
});

// Shortcodes
add_shortcode('pi_login_button', fn() => '<div class="pi-login-container"><button id="pi-login-btn" class="pi-login-button">' . __('Login with Pi Network', 'pi-network-plugin') . '</button></div>');

add_shortcode('pi_pay_now', fn() => '<div class="pi-login-pay-container"><button id="pi-login-pay-btn" class="pi-login-pay-button">' . __('Login & Pay with Pi', 'pi-network-plugin') . '</button></div>');

// DB setup
register_activation_hook(__FILE__, function () {
    global $wpdb;
    $table = $wpdb->prefix . 'pi_payments';
    $charset = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        username VARCHAR(100) NOT NULL,
        payment_id VARCHAR(255) NOT NULL,
        amount FLOAT DEFAULT 0,
        memo TEXT,
        status VARCHAR(50),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE (payment_id)
    ) $charset;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
});

// Export CSV
function pi_export_payments_csv() {
    if (!current_user_can('manage_options')) wp_die(__('Access denied', 'pi-network-plugin'));
    global $wpdb;
    $table = $wpdb->prefix . 'pi_payments';
    $results = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC", ARRAY_A);
    if (empty($results)) wp_die(__('No payments found.', 'pi-network-plugin'));

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="pi-payments.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, array_keys($results[0]));
    foreach ($results as $r) fputcsv($out, $r);
    fclose($out); exit;
}

// Admin menu + export submenu
add_action('admin_menu', function () {
    add_options_page(
        __('Pi Network Plugin Settings', 'pi-network-plugin'),
        __('Pi Network', 'pi-network-plugin'),
        'manage_options',
        'pi-network-settings',
        'pi_render_settings_page'
    );

    add_submenu_page(
        'pi-network-settings',
        __('Export Payments', 'pi-network-plugin'),
        __('Export CSV', 'pi-network-plugin'),
        'manage_options',
        'pi-export-csv',
        'pi_export_payments_csv'
    );
});
