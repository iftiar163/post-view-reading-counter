<?php
/**
 * Registers and renders the admin dashboard widget.
 *
 * @package PostViewCounter
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PVC_Dashboard_Widget {
    public static function init() {
        if( ! current_user_can('manage_options') ) {
            return;
        }

        add_action('wp_dashboard_setup', [__CLASS__, 'register_widget']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_styles']);
    }

    /**
     * Register the widget with WordPress dashboard.
     */
    public static function register_widget() {
        wp_add_dashboard_widget(
            'pvc_dashboard_widget',
            __('Post View Stats', 'post-views-counter'),
            [__CLASS__, 'render_widget']
        );
    }

    /**
     * Query all the stats we need for the widget.
     * Separated from render so logic and display are kept apart.
     *
     * @return array Stats data array.
     */

    private static function get_stats() {
        $total_posts = wp_count_posts('post');
        $post_count = (int) $total_posts->publish;

        // Try to get cached stats first
        $total_views = wp_cache_get('pvc_total_views', 'post-views-counter');
        if (false === $total_views) {
            global $wpdb;

            $total_views = (int) $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT SUM(CAST(meta_value AS UNSIGNED))
                     FROM {$wpdb->postmeta}
                     WHERE meta_key = %s
                     AND meta_value != ''",
                    PVC_META_VIEWS
                )
            );

            // Cache for 1 hour
            wp_cache_set('pvc_total_views', $total_views, 'post-views-counter', 3600);
        }

        // Try to get cached reading time first
        $total_time = wp_cache_get('pvc_total_time', 'post-views-counter');
        if (false === $total_time) {
            global $wpdb;

            $total_time = (int) $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT SUM(CAST(meta_value AS UNSIGNED))
                     FROM {$wpdb->postmeta}
                     WHERE meta_key = %s
                     AND meta_value != ''",
                    PVC_META_TIME
                )
            );

            // Cache for 1 hour
            wp_cache_set('pvc_total_time', $total_time, 'post-views-counter', 3600);
        }

        return [
            'post_count' => $post_count,
            'total_views' => $total_views,
            'total_time' => $total_time,
        ];
    }

    /**
     * Convert total seconds into a human readable string.
     * e.g. 7800 seconds → "2 hrs 10 mins"
     *
     * @param  int    $total_seconds Raw seconds from all users across all posts.
     * @return string Formatted time string.
     */

    private static function format_reading_time( $total_seconds ) {
        if( $total_seconds <= 0 ) {
            return '0 mins';
        }

        $hours = (int) intdiv( $total_seconds, 3600 );
        $minutes = (int) intdiv( $total_seconds % 3600, 60 );

        if($hours === 0) {
            return $minutes . ' mins';
        }

        if($minutes === 0) {
            return $hours . ' hrs';
        }

        return $hours . ' hrs ' . $minutes . ' mins';

    }

    /**
     * Render the dashboard widget HTML.
     */
    public static function render_widget() {

        $stats = self::get_stats();
        $time  = self::format_reading_time( $stats['total_time'] );

        ?>
        <div class="pvc-widget-container">
            <div class="pvc-widget-header">
                <h3 class="pvc-widget-title">📊 Content Analytics</h3>
                <p class="pvc-widget-subtitle">Real-time engagement metrics</p>
            </div>

            <div class="pvc-stats-grid">
                <div class="pvc-stat-card pvc-stat-posts">
                    <div class="pvc-stat-header">
                        <span class="pvc-stat-icon">📝</span>
                        <span class="pvc-stat-title">Published Posts</span>
                    </div>
                    <div class="pvc-stat-content">
                        <div class="pvc-stat-number"><?php echo number_format( (float) $stats['post_count'] ); ?></div>
                        <div class="pvc-stat-description">Total articles</div>
                    </div>
                </div>

                <div class="pvc-stat-card pvc-stat-views">
                    <div class="pvc-stat-header">
                        <span class="pvc-stat-icon">👁️</span>
                        <span class="pvc-stat-title">Total Views</span>
                    </div>
                    <div class="pvc-stat-content">
                        <div class="pvc-stat-number"><?php echo number_format( (float) $stats['total_views'] ); ?></div>
                        <div class="pvc-stat-description">Visitor pageviews</div>
                    </div>
                </div>

                <div class="pvc-stat-card pvc-stat-time">
                    <div class="pvc-stat-header">
                        <span class="pvc-stat-icon">⏱️</span>
                        <span class="pvc-stat-title">Read Time</span>
                    </div>
                    <div class="pvc-stat-content">
                        <div class="pvc-stat-number"><?php echo esc_html( $time ); ?></div>
                        <div class="pvc-stat-description">Total user reading time</div>
                    </div>
                </div>
            </div>

            <div class="pvc-widget-footer">
                <a href="<?php echo esc_attr(admin_url('edit.php')); ?>" class="pvc-footer-link">
                    View all posts →
                </a>
            </div>
        </div>
        <?php
    }

    /**
     * Enqueue styles for the dashboard widget.
     *
     * @param string $hook The current admin page hook.
     */
    public static function enqueue_styles( $hook ) {

        // Only load on the main dashboard page
        if ( 'index.php' !== $hook ) {
            return;
        }

        // Modern CSS with gradients and shadows
        $css = '
            .pvc-widget-container {
                background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
                border-radius: 12px;
                overflow: hidden;
            }

            .pvc-widget-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 20px;
                margin: 0;
            }

            .pvc-widget-title {
                margin: 0 0 4px 0;
                font-size: 18px;
                font-weight: 700;
                letter-spacing: -0.3px;
            }

            .pvc-widget-subtitle {
                margin: 0;
                font-size: 12px;
                opacity: 0.9;
                font-weight: 500;
            }

            .pvc-stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
                gap: 16px;
                padding: 24px;
            }

            .pvc-stat-card {
                background: white;
                border-radius: 10px;
                padding: 20px;
                border: 1px solid #e5e7eb;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
                cursor: pointer;
                position: relative;
                overflow: hidden;
            }

            .pvc-stat-card::before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 3px;
                background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            }

            .pvc-stat-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
                border-color: #667eea;
            }

            .pvc-stat-posts {
                --accent: #3b82f6;
            }

            .pvc-stat-posts .pvc-stat-header::after {
                background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            }

            .pvc-stat-views {
                --accent: #10b981;
            }

            .pvc-stat-views .pvc-stat-header::after {
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            }

            .pvc-stat-time {
                --accent: #f59e0b;
            }

            .pvc-stat-time .pvc-stat-header::after {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            }

            .pvc-stat-header {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 16px;
                position: relative;
            }

            .pvc-stat-header::after {
                content: "";
                position: absolute;
                right: 0;
                width: 40px;
                height: 40px;
                border-radius: 8px;
                opacity: 0.1;
            }

            .pvc-stat-icon {
                font-size: 24px;
                line-height: 1;
                z-index: 1;
            }

            .pvc-stat-title {
                font-size: 12px;
                font-weight: 600;
                color: #6b7280;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                z-index: 1;
            }

            .pvc-stat-content {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            .pvc-stat-number {
                font-size: 28px;
                font-weight: 800;
                color: #1f2937;
                letter-spacing: -0.5px;
            }

            .pvc-stat-description {
                font-size: 11px;
                color: #9ca3af;
                font-weight: 500;
            }

            .pvc-widget-footer {
                background: #f3f4f6;
                padding: 16px 24px;
                border-top: 1px solid #e5e7eb;
                text-align: center;
            }

            .pvc-footer-link {
                color: #667eea;
                text-decoration: none;
                font-size: 13px;
                font-weight: 600;
                transition: all 0.2s ease;
            }

            .pvc-footer-link:hover {
                color: #764ba2;
                margin-right: 4px;
            }

            @media (max-width: 600px) {
                .pvc-stats-grid {
                    grid-template-columns: 1fr;
                    gap: 12px;
                    padding: 16px;
                }

                .pvc-widget-header {
                    padding: 16px;
                }

                .pvc-stat-number {
                    font-size: 24px;
                }
            }
        ';

        wp_add_inline_style( 'wp-admin', $css );
    }

    
}