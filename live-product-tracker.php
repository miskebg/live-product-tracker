<?php
/**
 * Plugin Name: Live Product Tracker
 * Description: Prikazuje koliko korisnika trenutno gleda proizvod, dodalo u omiljene ili korpu.
 * Version: 1.1
 * Author: Milos Komljen
 * Author URI: https://github.com/miskebg/live-product-tracker
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Include settings page
require_once plugin_dir_path(__FILE__) . 'admin/settings-page.php';

if (!defined('ABSPATH')) exit;

define('LIVE_TRACKER_VERSION', '1.1');

class LiveProductTracker {
    public function __construct() {
        add_action('template_redirect', [$this, 'track_view']);
        add_shortcode('live_product_viewers', [$this, 'display_viewers']);

        add_shortcode('favorite_button', [$this, 'favorite_button']);
        add_shortcode('product_favorites_count', [$this, 'favorites_count']);
        add_action('wp_ajax_toggle_favorite', [$this, 'toggle_favorite']);
        add_action('wp_ajax_nopriv_toggle_favorite', [$this, 'toggle_favorite']);

        add_action('woocommerce_add_to_cart', [$this, 'track_add_to_cart'], 10, 6);
        add_shortcode('product_cart_count', [$this, 'display_cart_count']);

        add_action('wp_enqueue_scripts', function() {
            wp_enqueue_script(
                'lpt-script',
                plugin_dir_url(__FILE__) . 'lpt.js',
                ['jquery'],
                LIVE_TRACKER_VERSION,
                true
            );
            wp_localize_script('lpt-script', 'lpt_ajax', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('lpt_nonce')
            ]);
        });

        add_action('init', [$this, 'start_session'], 1);
    }

    public function start_session() {
        if (!session_id()) session_start();
    }

    public function track_view() {
        if (!is_product()) return;
        $product_id = get_the_ID();
        $session_id = session_id();
        $key = 'lpt_view_' . $product_id;
        $data = get_transient($key);
        if (!is_array($data)) $data = [];
        $data[$session_id] = time();
        set_transient($key, $data, 300);
    }

    public function display_viewers() {
        if (!get_option('lpt_show_viewers')) return '';
        if (!is_product()) return '';
        $product_id = get_the_ID();
        $key = 'lpt_view_' . $product_id;
        $data = get_transient($key);
        $active = 0;
        $now = time();
        if (is_array($data)) {
            foreach ($data as $sid => $timestamp) {
                if (($now - $timestamp) <= 300) $active++;
            }
        }
        return "<div class='lpt-viewers'>$active korisnik(a) trenutno gleda ovaj proizvod.</div>";
    }

    public function favorite_button() {
        if (!get_option('lpt_enable_favorites')) return '';
        if (!is_user_logged_in() || !is_product()) return '';
        $user_id = get_current_user_id();
        $product_id = get_the_ID();
        $favorites = get_user_meta($user_id, 'lpt_favorites', true);
        if (!is_array($favorites)) $favorites = [];
        $is_fav = in_array($product_id, $favorites);
        $text = $is_fav ? 'Ukloni iz omiljenih' : 'Dodaj u omiljene';
        return "<button class='lpt-fav-btn' data-product='$product_id'>$text</button>";
    }

    public function toggle_favorite() {
        check_ajax_referer('lpt_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error('Morate biti ulogovani.');
        }

        if (!isset($_POST['product_id'])) {
            wp_send_json_error('ID proizvoda nije prosleđen.');
        }

        $product_id = intval($_POST['product_id']);
        if (!$product_id) {
            wp_send_json_error('Neispravan ID proizvoda.');
        }

        $user_id = get_current_user_id();
        $favorites = get_user_meta($user_id, 'lpt_favorites', true);
        if (!is_array($favorites)) $favorites = [];

        if (in_array($product_id, $favorites)) {
            $favorites = array_diff($favorites, [$product_id]);
        } else {
            $favorites[] = $product_id;
        }

        update_user_meta($user_id, 'lpt_favorites', $favorites);
        wp_send_json_success();
    }

    public function favorites_count() {
        if (!get_option('lpt_enable_favorites')) return '';
        if (!is_product()) return '';
        $product_id = get_the_ID();
        $users = get_users();
        $count = 0;
        foreach ($users as $user) {
            $favorites = get_user_meta($user->ID, 'lpt_favorites', true);
            if (is_array($favorites) && in_array($product_id, $favorites)) {
                $count++;
            }
        }
        return "<div class='lpt-fav-count'>$count korisnik(a) je dodalo u omiljene.</div>";
    }

    public function track_add_to_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
        if (!$product_id) return;
        $session_id = session_id();
        if (!$session_id) session_start();
        $key = 'lpt_cart_' . $product_id;
        $data = get_transient($key);
        if (!is_array($data)) $data = [];
        $data[$session_id] = time();
        set_transient($key, $data, 1800);
    }

    public function display_cart_count() {
        if (!get_option('lpt_show_cart_count')) return '';
        if (!is_product()) return '';
        $product_id = get_the_ID();
        $key = 'lpt_cart_' . $product_id;
        $data = get_transient($key);
        $active = 0;
        $now = time();
        if (is_array($data)) {
            foreach ($data as $sid => $timestamp) {
                if (($now - $timestamp) <= 1800) $active++;
            }
        }
        return "<div class='lpt-cart-count'>$active korisnik(a) je dodalo ovaj proizvod u korpu.</div>";
    }
}

// Admin settings menu
add_action('admin_menu', function() {
    add_options_page(
        'Live Product Tracker Settings',
        'Live Product Tracker',
        'manage_options',
        'live-product-tracker',
        'lpt_settings_page'
    );
});

// Register settings
add_action('admin_init', 'lpt_register_settings');

// Enable shortcodes in WooCommerce short description
add_filter('woocommerce_short_description', 'do_shortcode');

new LiveProductTracker();
