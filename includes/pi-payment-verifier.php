<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

/**
 * ✅ Verifies payment with Pi Network using payment ID
 */
if (!function_exists('pi_verify_payment')) {
    function pi_verify_payment($payment_id) {
        $settings             = pi_get_plugin_settings();
        $private_key          = $settings['private_key'];
        $memo                 = $settings['memo'];
        $email_user_enabled  = !empty($settings['email_user']);
        $email_admin_enabled = !empty($settings['email_admin']);

        $url = "https://api.minepi.com/v2/payments/{$payment_id}";

        $headers = [
            "Authorization: Key {$private_key}",
            "Content-Type: application/json"
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status === 200) {
            $data = json_decode($response, true);
            if (
                isset($data['transaction']['status']) &&
                $data['transaction']['status'] === 'completed'
            ) {
                global $wpdb;
                $table = $wpdb->prefix . 'pi_payments';

                $username = isset($data['user']['username']) ? sanitize_text_field($data['user']['username']) : __('unknown', 'pi-network-plugin');
                $amount   = isset($data['amount']) ? floatval($data['amount']) : 0;
                $memo     = isset($data['memo']) ? sanitize_text_field($data['memo']) : '';

                $wpdb->insert($table, [
                    'username'   => $username,
                    'payment_id' => sanitize_text_field($data['identifier']),
                    'amount'     => $amount,
                    'memo'       => $memo,
                    'status'     => 'completed'
                ]);

                // ✉️ Get user email (if exists)
                $user_email = '';
                $user_data  = get_user_by('login', $username);
                if ($user_data) {
                    $user_email = $user_data->user_email;
                }

                // 📩 Email to user
                if ($email_user_enabled && !empty($user_email)) {
                    $subject = __('✅ Your Pi Payment was Successful!', 'pi-network-plugin');
                    $message = sprintf(
                        '<html><body>
                        <p>%s <strong>%s</strong>,</p>
                        <p>%s <strong>%s π</strong>.</p>
                        <p><strong>%s</strong> %s</p>
                        <p>%s</p>
                        </body></html>',
                        __('Hi', 'pi-network-plugin'),
                        esc_html($username),
                        __('We’ve received your payment of', 'pi-network-plugin'),
                        esc_html($amount),
                        __('Memo:', 'pi-network-plugin'),
                        esc_html($memo),
                        __('Thank you for using Pi Network via our site!', 'pi-network-plugin')
                    );

                    wp_mail(
                        $user_email,
                        $subject,
                        $message,
                        ['Content-Type: text/html; charset=UTF-8']
                    );
                }

                // 🛎️ Email to admin
                if ($email_admin_enabled) {
                    $subject = __('💸 New Pi Payment Received', 'pi-network-plugin');
                    $message = sprintf(
                        '<html><body>
                        <h2>%s</h2>
                        <p><strong>%s</strong> %s</p>
                        <p><strong>%s</strong> %s π</p>
                        <p><strong>%s</strong> %s</p>
                        <p><strong>%s</strong> %s</p>
                        </body></html>',
                        __('🪙 Payment Verified', 'pi-network-plugin'),
                        __('User:', 'pi-network-plugin'), esc_html($username),
                        __('Amount:', 'pi-network-plugin'), esc_html($amount),
                        __('Memo:', 'pi-network-plugin'), esc_html($memo),
                        __('Date:', 'pi-network-plugin'), esc_html(current_time('mysql'))
                    );

                    wp_mail(
                        get_option('admin_email'),
                        $subject,
                        $message,
                        ['Content-Type: text/html; charset=UTF-8']
                    );
                }

                return true;
            }
        }

        return false;
    }
}

/**
 * ⚡ AJAX Hook: Verifies Pi payment ID from frontend
 */
if (!function_exists('pi_verify_payment_ajax')) {
    function pi_verify_payment_ajax() {
        $payment_id = isset($_POST['payment_id']) ? sanitize_text_field($_POST['payment_id']) : '';

        if (empty($payment_id)) {
            wp_send_json_error(__('Missing payment ID', 'pi-network-plugin'));
        }

        if (pi_verify_payment($payment_id)) {
            wp_send_json_success(__('Payment verified successfully!', 'pi-network-plugin'));
        } else {
            wp_send_json_error(__('Payment verification failed.', 'pi-network-plugin'));
        }
    }
}
add_action('wp_ajax_pi_verify_payment', 'pi_verify_payment_ajax');
add_action('wp_ajax_nopriv_pi_verify_payment', 'pi_verify_payment_ajax');
