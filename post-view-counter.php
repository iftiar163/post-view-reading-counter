<?php
/**
 * Plugin Name: Post View Counter
 * Plugin URI:  https://wordpress.org/plugins/post-view-counter/
 * Description: Track post views and accumulate total reading time from visitors. Display engagement metrics in the admin dashboard and post list columns.
 * Version:     1.0.0
 * Author:      Iftiar Hossain
 * Author URI:  https://yourwebsite.com
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0-html
 * Text Domain: post-view-counter
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ─── Constants: Define once, use everywhere ────────────────────────────────
define( 'PVC_VERSION',    '1.0.0' );
define( 'PVC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PVC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PVC_META_VIEWS', '_pvc_view_count' );
define( 'PVC_META_TIME',  '_pvc_reading_time' );

// ─── Load our class files ──────────────────────────────────────────────────
require_once PVC_PLUGIN_DIR . 'includes/class-tracker.php';
require_once PVC_PLUGIN_DIR . 'includes/class-admin-columns.php';
require_once PVC_PLUGIN_DIR . 'includes/class-dashboard-widget.php';

// ─── Bootstrap: start the plugin ──────────────────────────────────────────
add_action( 'plugins_loaded', 'pvc_init' );

function pvc_init() {
    PVC_Tracker::init();
    PVC_Admin_Columns::init();
    PVC_Dashboard_Widget::init();
}