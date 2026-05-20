<?php
/**
 * Handles view tracking via AJAX and reading time calculation.
 *
 * @package PostViewCounter
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PVC_Tracker {
    /**
     * Register all hooks for this class.
     * Called once from pvc_init() in the main file.
     */
    public static function init(){
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_scripts']);
        add_action( 'wp_ajax_pvc_track_view', [__CLASS__, 'track_view'] );
        add_action( 'wp_ajax_nopriv_pvc_track_view', [__CLASS__, 'track_view'] );
        add_action( 'wp_ajax_pvc_track_reading_time', [__CLASS__, 'track_reading_time'] );
        add_action( 'wp_ajax_nopriv_pvc_track_reading_time', [__CLASS__, 'track_reading_time'] );
    }

    /**
     * Load our tracker JS on single post pages only.
     */
    public static function enqueue_scripts() {

        // Only load on single post pages, not archives, homepage, etc.
        if ( ! is_singular( 'post' ) ) {
            return;
        }

        wp_enqueue_script(
            'pvc-tracker',                              
            PVC_PLUGIN_URL . 'assets/js/tracker.js',   
            [],                          
            PVC_VERSION,                                
            true                                        
        );

        // Pass PHP variables to JavaScript safely
        wp_localize_script(
            'pvc-tracker',
            'pvcData',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'pvc_track_view' ),
                'postId'  => get_the_ID(),
            )
        );
    }

        /**
     * Track and accumulate reading time when a visitor leaves a post.
     * This AJAX handler receives the time spent by a visitor and adds it to the cumulative total.
     */
    public static function track_reading_time() {
        // Verify nonce for security
        if( !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'pvc_track_view') ) {
            wp_send_json_error(['message' => 'Invalid nonce'], 403);
        }

        // Skip tracking for admins, editors, authors, contributors
        if( self::should_skip_tracking() ) {
            wp_send_json_success(['message' => 'Reading time tracking skipped for this user role']);
        }

        // Sanitize and validate post ID
        $post_id = isset($_POST['post_id']) ? (int) sanitize_text_field($_POST['post_id']) : 0;

        if($post_id === 0) {
            wp_send_json_error(['message' => 'Invalid post ID'], 400);
        }

        // Check if post is published
        if('publish' !== get_post_status($post_id)) {
            wp_send_json_error(['message' => 'Post is not published'], 404);
        }

        // Sanitize and validate time spent (in seconds)
        $time_spent = isset($_POST['time_spent']) ? (int) sanitize_text_field($_POST['time_spent']) : 0;

        if($time_spent < 3 || $time_spent > 1800) {
            wp_send_json_error(['message' => 'Invalid time spent'], 400);
        }

        // Get current cumulative reading time
        $current_total = (int) get_post_meta($post_id, PVC_META_TIME, true);

        // Add the visitor's reading time to the total
        $new_total = $current_total + $time_spent;

        // Store the updated total
        update_post_meta($post_id, PVC_META_TIME, $new_total);

        // Send a success response with the new total reading time
        wp_send_json_success(['reading_time' => $new_total]);
    }

    /**
     * Check if the current user should be excluded from view tracking.
     * Administrators, editors, authors, and contributors are excluded.
     *
     * @return bool True if we should skip tracking for this user.
     */

    private static function should_skip_tracking() {
        // If user is not logged in, they are a real visitor — count them
        if( !is_user_logged_in() ) {
            return false;
        }
        // Roles that should NOT be counted as real visitors
        $excluded_roles = ['administrator', 'editor', 'author', 'contributor'];
        $user = wp_get_current_user();

        $matched = array_intersect($excluded_roles, (array) $user->roles);

        return !empty($matched);

    }

    /**
     * AJAX handler: increment the view count for a post.
     * Hooked to both wp_ajax_ and wp_ajax_nopriv_.
     */
    public static function track_view() {
        // Verify nonce for security
            if( !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'pvc_track_view') ) {
            wp_send_json_error(['message' => 'Invalid nonce'], 403);
        }

        // Skip tracking for admins, editors, authors, contributors
        if( self::should_skip_tracking() ) {
            wp_send_json_success(['message' => 'Tracking skipped for this user role']);
        }

        // Sanitize the post ID
            $post_id = isset($_POST['post_id']) ? (int) sanitize_text_field($_POST['post_id']) : 0;

        if($post_id === 0) {
            wp_send_json_error(['message' => 'Invalid post ID'], 400);
        }

        // Increment the view count
        if('publish' !== get_post_status($post_id)) {
            wp_send_json_error(['message' => 'Post is not published'], 404);
        }

            $current_views = (int) get_post_meta($post_id, PVC_META_VIEWS, true);
        $new_views = $current_views + 1;

        update_post_meta($post_id, PVC_META_VIEWS, $new_views);

        // Send a success response with the new view count
        wp_send_json_success(['views' => $new_views]);
    }
}