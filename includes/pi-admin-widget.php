<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

/**
 * ✅ Adds a dashboard widget for Pi payment summaries
 */
if (!function_exists('pi_add_dashboard_widget')) {
    function pi_add_dashboard_widget() {
        wp_add_dashboard_widget(
            'pi_payments_widget',
            __('🪙 Pi Network Payments', 'pi-network-plugin'),
            'pi_render_payments_widget'
        );
    }
}
add_action('wp_dashboard_setup', 'pi_add_dashboard_widget');

/**
 * 🧾 Renders the widget content
 */
if (!function_exists('pi_render_payments_widget')) {
    function pi_render_payments_widget() {
        if (!current_user_can('manage_options')) {
            echo '<p>' . __('Access denied.', 'pi-network-plugin') . '</p>';
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'pi_payments';
        $payments = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC LIMIT 5");

        if (empty($payments)) {
            echo '<p>' . __('No recent Pi payments found.', 'pi-network-plugin') . '</p>';
            return;
        }

        echo '<table class="widefat fixed striped">';
        echo '<thead><tr>';
        echo '<th>' . __('Username', 'pi-network-plugin') . '</th>';
        echo '<th>' . __('Amount (π)', 'pi-network-plugin') . '</th>';
        echo '<th>' . __('Memo', 'pi-network-plugin') . '</th>';
        echo '<th>' . __('Date', 'pi-network-plugin') . '</th>';
        echo '</tr></thead><tbody>';

        foreach ($payments as $payment) {
            echo '<tr>';
            echo '<td>' . esc_html($payment->username) . '</td>';
            echo '<td>' . esc_html($payment->amount) . '</td>';
            echo '<td>' . esc_html($payment->memo) . '</td>';
            echo '<td>' . esc_html(date('Y-m-d H:i', strtotime($payment->created_at))) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        echo '<p><a href="' . esc_url(admin_url('admin.php?page=pi-export-csv')) . '" class="button button-primary" style="margin-top: 10px;">' . __('Export All to CSV', 'pi-network-plugin') . '</a></p>';
    }
}
