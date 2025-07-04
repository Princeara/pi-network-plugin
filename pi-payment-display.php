<?php

if (!function_exists('pi_render_payment_history')) {
    function pi_render_payment_history() {
        if (!is_user_logged_in()) {
            return '<p>You need to be logged in to view your payment history.</p>';
        }

        $current_user = wp_get_current_user();
        $username     = $current_user->user_login;

        global $wpdb;
        $table = $wpdb->prefix . 'pi_payments';

        $payments = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE username = %s ORDER BY created_at DESC",
            $username
        ));

        if (empty($payments)) {
            return '<p>No payments found.</p>';
        }

        ob_start();
        ?>
        <table class="pi-payment-table">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Amount (π)</th>
                    <th>Memo</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment) : ?>
                    <tr>
                        <td><?php echo esc_html($payment->payment_id); ?></td>
                        <td><?php echo esc_html($payment->amount); ?></td>
                        <td><?php echo esc_html($payment->memo); ?></td>
                        <td><?php echo esc_html($payment->status); ?></td>
                        <td><?php echo esc_html(date("Y-m-d H:i", strtotime($payment->created_at))); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        return ob_get_clean();
    }
}
add_shortcode('pi_payment_history', 'pi_render_payment_history');
