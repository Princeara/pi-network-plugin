<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

/**
 * ✅ Handle Pi Network login request from frontend
 */
if (!function_exists('pi_handle_login')) {
    function pi_handle_login() {
        // Get username posted from Pi SDK
        $username = isset($_POST['username']) ? sanitize_user($_POST['username']) : '';

        if (empty($username)) {
            wp_send_json_error(__('Missing username from Pi login.', 'pi-network-plugin'));
        }

        // Check if user exists, else create one
        $user = get_user_by('login', $username);

        if (!$user) {
            $random_password = wp_generate_password(12, true);
            $user_id = wp_insert_user([
                'user_login' => $username,
                'user_pass'  => $random_password,
                'role'       => 'subscriber',
            ]);

            if (is_wp_error($user_id)) {
                wp_send_json_error(__('Failed to create user account.', 'pi-network-plugin'));
            }

            $user = get_user_by('id', $user_id);
        }

        // Log in the user
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, true);
        do_action('wp_login', $user->user_login, $user);

        wp_send_json_success(__('Login successful.', 'pi-network-plugin'));
    }
}
add_action('wp_ajax_pi_handle_login', 'pi_handle_login');
add_action('wp_ajax_nopriv_pi_handle_login', 'pi_handle_login');
