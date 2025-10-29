<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// admin/settings-page.php

function liveprtr_register_settings() {
    register_setting('liveprtr_settings_group', 'liveprtr_show_viewers', 'liveprtr_sanitize_checkbox');
    register_setting('liveprtr_settings_group', 'liveprtr_enable_favorites', 'liveprtr_sanitize_checkbox');
    register_setting('liveprtr_settings_group', 'liveprtr_show_cart_count', 'liveprtr_sanitize_checkbox');
}

function liveprtr_sanitize_checkbox($value) {
    return ($value === '1') ? '1' : '0';
}

function liveprtr_settings_page() {
    ?>
    <div class="wrap">
        <h1>Live Product Tracker Settings</h1>
        <form method="post" action="options.php">
            <?php
                settings_fields('liveprtr_settings_group');
                do_settings_sections('liveprtr_settings_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Show product viewers count</th>
                    <td>
                        <input type="checkbox" name="liveprtr_show_viewers" value="1" <?php checked(1, get_option('liveprtr_show_viewers'), true); ?> />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Enable favorites (login required)</th>
                    <td>
                        <input type="checkbox" name="liveprtr_enable_favorites" value="1" <?php checked(1, get_option('liveprtr_enable_favorites'), true); ?> />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Show cart addition count</th>
                    <td>
                        <input type="checkbox" name="liveprtr_show_cart_count" value="1" <?php checked(1, get_option('liveprtr_show_cart_count'), true); ?> />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
