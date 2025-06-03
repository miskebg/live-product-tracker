<?php
// admin/settings-page.php

function lpt_register_settings() {
    register_setting('lpt_settings_group', 'lpt_show_viewers', 'lpt_sanitize_checkbox');
    register_setting('lpt_settings_group', 'lpt_enable_favorites', 'lpt_sanitize_checkbox');
    register_setting('lpt_settings_group', 'lpt_show_cart_count', 'lpt_sanitize_checkbox');
}

function lpt_sanitize_checkbox($value) {
    return ($value === '1') ? '1' : '0';
}

function lpt_settings_page() {
    ?>
    <div class="wrap">
        <h1>Live Product Tracker Settings</h1>
        <form method="post" action="options.php">
            <?php
                settings_fields('lpt_settings_group');
                do_settings_sections('lpt_settings_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Show product viewers count</th>
                    <td><input type="checkbox" name="lpt_show_viewers" value="1" <?php checked(1, get_option('lpt_show_viewers'), true); ?> /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Enable favorites (login required)</th>
                    <td><input type="checkbox" name="lpt_enable_favorites" value="1" <?php checked(1, get_option('lpt_enable_favorites'), true); ?> /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Show cart addition count</th>
                    <td><input type="checkbox" name="lpt_show_cart_count" value="1" <?php checked(1, get_option('lpt_show_cart_count'), true); ?> /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
