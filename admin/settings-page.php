<?php
if (!defined('ABSPATH')) exit;

/**
 * 🛠️ Register plugin settings
 */
add_action('admin_init', function () {
    register_setting('pi_settings_group', 'pi_app_id');
    register_setting('pi_settings_group', 'pi_private_key');
    register_setting('pi_settings_group', 'pi_payment_amount');
    register_setting('pi_settings_group', 'pi_payment_memo');
    register_setting('pi_settings_group', 'pi_sandbox_mode');
    register_setting('pi_settings_group', 'pi_email_user');
    register_setting('pi_settings_group', 'pi_email_admin');
});

/**
 * 🧩 Render plugin settings page
 */
if (!function_exists('pi_render_settings_page')) {
    function pi_render_settings_page() { ?>
        <div class="wrap">
            <h1><?php _e('Pi Network Plugin Settings', 'pi-network-plugin'); ?></h1>

            <form method="post" action="options.php">
                <?php
                settings_fields('pi_settings_group');
                do_settings_sections('pi_settings_group');
                ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="pi_app_id"><?php _e('Pi App ID', 'pi-network-plugin'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="pi_app_id" id="pi_app_id" value="<?php echo esc_attr(get_option('pi_app_id')); ?>" class="regular-text" />
                            <p class="description"><?php _e('Enter your Pi Network App ID from the developer portal.', 'pi-network-plugin'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="pi_private_key"><?php _e('Private Key', 'pi-network-plugin'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="pi_private_key" id="pi_private_key" value="<?php echo esc_attr(get_option('pi_private_key')); ?>" class="regular-text" />
                            <p class="description"><?php _e('Your Pi API Private Key for payment verification.', 'pi-network-plugin'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="pi_payment_amount"><?php _e('Default Payment Amount (π)', 'pi-network-plugin'); ?></label>
                        </th>
                        <td>
                            <input type="number" step="0.01" name="pi_payment_amount" id="pi_payment_amount" value="<?php echo esc_attr(get_option('pi_payment_amount', 1)); ?>" />
                            <p class="description"><?php _e('This amount will be requested by default in the Pi payment modal.', 'pi-network-plugin'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="pi_payment_memo"><?php _e('Payment Memo', 'pi-network-plugin'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="pi_payment_memo" id="pi_payment_memo" value="<?php echo esc_attr(get_option('pi_payment_memo', 'Payment via WordPress')); ?>" class="regular-text" />
                            <p class="description"><?php _e('Displayed to the user during payment.', 'pi-network-plugin'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="pi_sandbox_mode"><?php _e('Use Sandbox Mode?', 'pi-network-plugin'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" name="pi_sandbox_mode" id="pi_sandbox_mode" value="1" <?php checked(1, get_option('pi_sandbox_mode')); ?> />
                            <p class="description"><?php _e('Enable this option when testing with sandbox credentials.', 'pi-network-plugin'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="pi_email_user"><?php _e('Send Receipt to User?', 'pi-network-plugin'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" name="pi_email_user" id="pi_email_user" value="1" <?php checked(1, get_option('pi_email_user')); ?> />
                            <p class="description"><?php _e('Email confirmation to the user after a successful payment.', 'pi-network-plugin'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="pi_email_admin"><?php _e('Send Notification to Admin?', 'pi-network-plugin'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" name="pi_email_admin" id="pi_email_admin" value="1" <?php checked(1, get_option('pi_email_admin')); ?> />
                            <p class="description"><?php _e('Notify the admin when a new Pi payment is verified.', 'pi-network-plugin'); ?></p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(__('Save Changes', 'pi-network-plugin')); ?>
            </form>
        </div>
    <?php }
}
