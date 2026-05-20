<?php
/**
 * Adds Views and Reading Time columns to the post list table.
 *
 * @package PostViewCounter
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PVC_Admin_Columns {
    public static function init() {
        add_filter('manage_posts_columns', [__CLASS__, 'add_columns']);
        add_action('manage_posts_custom_column', [__CLASS__, 'render_columns'], 10, 2);
        add_filter('manage_edit-post_sortable_columns', [__CLASS__, 'sortable_columns']);
        add_action('pre_get_posts', [__CLASS__, 'sort_query']);
    }

    /**
     * Add Views and Reading Time to the columns array.
     *
     * @param  array $columns Existing columns.
     * @return array Modified columns.
     */

    public static function add_columns($columns) {
        // Remove 'date' column temporarily so we can add it back at the end
        $date = $columns['date'];
        unset($columns['date']);

        // Add our custom columns
        $columns['pvc_views'] = __('👁️ Views', 'post-views-counter');
        $columns['pvc_reading_time'] = __('⏱️ Read Time', 'post-views-counter');
        // Add the 'date' column back at the end
        $columns['date'] = $date;
        return $columns;    
    }

    /**
     * Output data for each custom column row.
     *
     * @param string $column  The column key.
     * @param int    $post_id The current post ID.
     */

    public static function render_columns($column, $post_id) {
        switch( $column ) {
            case 'pvc_views':
                $views = (int) get_post_meta($post_id, PVC_META_VIEWS, true);
                echo '<strong>' . number_format($views) . '</strong>';
                break;

                case 'pvc_reading_time':
                    $total_seconds = (int) get_post_meta($post_id, PVC_META_TIME, true);
                    if ( $total_seconds <= 0 ) {
                        echo '<span style="color:#999;">—</span>';
                    } else {
                       echo esc_html( self::format_reading_time($total_seconds) );
                    }
                    break;
        }

    }

    /**
     * Convert total seconds to human readable format (e.g., "1h 23m" or "45m").
     *
     * @param int $seconds Total seconds.
     * @return string Formatted reading time.
     */
    private static function format_reading_time($seconds) {
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . 'm';
    }

    /**
     * Register which columns are sortable.
     *
     * @param  array $columns Existing sortable columns.
     * @return array Modified sortable columns.
     */

    public static function sortable_columns($columns) {
        $columns['pvc_views'] = 'pvc_views';
        $columns['pvc_reading_time'] = 'pvc_reading_time';
        return $columns;

    }

    /**
     * Modify the query when sorting by our custom columns.
     *
     * @param WP_Query $query The current query object.
     */
    public static function sort_query($query) {
        if( ! is_admin() || ! $query->is_main_query() ) {
            return;
        }

        $orderby = $query->get('orderby');

        if( 'pvc_views' === $orderby ) {
            $query->set('meta_key', PVC_META_VIEWS);
            $query->set('orderby', 'meta_value_num');
        }

        if( 'pvc_reading_time' === $orderby ) {
            $query->set('meta_key', PVC_META_TIME);
            $query->set('orderby', 'meta_value_num');
        }

    }
 
       
}