<?php
/**
 * Stats Class
 *
 * Handles statistics and analytics
 *
 * @package ShiftZoneRAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SZR_Stats {

    /**
     * Get dashboard statistics
     */
    public static function get_dashboard_stats() {
        // Brands stats
        $total_brands = wp_count_terms( array(
            'taxonomy'   => SZR_TAX_BRAND,
            'hide_empty' => false,
        ) );

        // Models stats
        $total_models = wp_count_terms( array(
            'taxonomy'   => SZR_TAX_MODEL,
            'hide_empty' => false,
        ) );

        // Photos stats
        $photo_counts = wp_count_posts( 'car_photo' );
        $published_photos = $photo_counts->publish ?? 0;
        $pending_photos = $photo_counts->pending ?? 0;
        $draft_photos = $photo_counts->draft ?? 0;

        // Users stats
        $user_counts = count_users();
        $total_users = $user_counts['total_users'] ?? 0;

        // Recent activity
        $recent_photos = get_posts( array(
            'post_type'      => 'car_photo',
            'posts_per_page' => 5,
            'post_status'    => 'any',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        // Top brands
        $top_brands = self::get_top_brands( 5 );

        // Photos by month
        $photos_by_month = self::get_photos_by_month( 6 );

        return array(
            'totals' => array(
                'brands'           => $total_brands,
                'models'           => $total_models,
                'published_photos' => $published_photos,
                'pending_photos'   => $pending_photos,
                'draft_photos'     => $draft_photos,
                'total_photos'     => $published_photos + $pending_photos + $draft_photos,
                'users'            => $total_users,
            ),
            'recent_photos'   => $recent_photos,
            'top_brands'      => $top_brands,
            'photos_by_month' => $photos_by_month,
        );
    }

    /**
     * Get top brands by photo count
     */
    public static function get_top_brands( $limit = 10 ) {
        $brands = get_terms( array(
            'taxonomy'   => SZR_TAX_BRAND,
            'hide_empty' => false,
            'orderby'    => 'count',
            'order'      => 'DESC',
            'number'     => $limit,
        ) );

        $brands_data = array();
        foreach ( $brands as $brand ) {
            $logo_id = get_term_meta( $brand->term_id, SZR_META_BRAND_LOGO, true );
            $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'thumbnail' ) : '';

            $brands_data[] = array(
                'id'       => $brand->term_id,
                'name'     => $brand->name,
                'count'    => $brand->count,
                'logo_url' => $logo_url,
            );
        }

        return $brands_data;
    }

    /**
     * Get photos grouped by month
     */
    public static function get_photos_by_month( $months = 12 ) {
        global $wpdb;

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT
                DATE_FORMAT(post_date, '%%Y-%%m') as month,
                COUNT(*) as count
            FROM {$wpdb->posts}
            WHERE post_type = 'car_photo'
            AND post_status = 'publish'
            AND post_date >= DATE_SUB(NOW(), INTERVAL %d MONTH)
            GROUP BY month
            ORDER BY month ASC",
            $months
        ) );

        $data = array();
        foreach ( $results as $result ) {
            $date = DateTime::createFromFormat( 'Y-m', $result->month );
            $data[] = array(
                'month' => $date ? $date->format( 'M Y' ) : $result->month,
                'count' => intval( $result->count ),
            );
        }

        return $data;
    }

    /**
     * Get user growth stats
     */
    public static function get_user_growth( $months = 12 ) {
        global $wpdb;

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT
                DATE_FORMAT(user_registered, '%%Y-%%m') as month,
                COUNT(*) as count
            FROM {$wpdb->users}
            WHERE user_registered >= DATE_SUB(NOW(), INTERVAL %d MONTH)
            GROUP BY month
            ORDER BY month ASC",
            $months
        ) );

        $data = array();
        foreach ( $results as $result ) {
            $date = DateTime::createFromFormat( 'Y-m', $result->month );
            $data[] = array(
                'month' => $date ? $date->format( 'M Y' ) : $result->month,
                'count' => intval( $result->count ),
            );
        }

        return $data;
    }

    /**
     * Get brand distribution
     */
    public static function get_brand_distribution() {
        $brands = get_terms( array(
            'taxonomy'   => SZR_TAX_BRAND,
            'hide_empty' => false,
            'orderby'    => 'count',
            'order'      => 'DESC',
        ) );

        $total = 0;
        foreach ( $brands as $brand ) {
            $total += $brand->count;
        }

        $data = array();
        foreach ( $brands as $brand ) {
            if ( $brand->count > 0 ) {
                $percentage = $total > 0 ? round( ( $brand->count / $total ) * 100, 1 ) : 0;
                $data[] = array(
                    'name'       => $brand->name,
                    'count'      => $brand->count,
                    'percentage' => $percentage,
                );
            }
        }

        return $data;
    }
}
