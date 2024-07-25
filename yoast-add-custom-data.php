<?php
/**
 * Plugin Name: Custom Yoast SEO Analysis
 * Description: Adds custom term meta data to Yoast SEO analysis.
 * Version: 1.0.0
 * Author: Andreas
 * Author URI: https://bo-we.dk
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class YoastAddCustomData {

    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_head', [$this, 'add_cat_header_data_to_admin']);
    }

    public function enqueue_scripts() {
        wp_enqueue_script('yoast-custom-data', plugins_url('register-custom-data.js', __FILE__), [], '1.0', true);
    }

    public function add_cat_header_data_to_admin() {
        $data = array('page_type' => ''); // Initialize with empty page_type

        $screen = get_current_screen();

        if ($screen->taxonomy == 'product_cat') {
            $data['page_type'] = 'product_cat';
            $term_id = filter_input(INPUT_GET, 'tag_ID', FILTER_SANITIZE_NUMBER_INT);
            $cat_meta = get_term_meta($term_id, 'cat_meta', true);

            if (!empty($cat_meta)) {
                $cat_meta_data = maybe_unserialize($cat_meta);
                $data['woo_category'] = [
                    'top_content' => isset($cat_meta_data['cat_header']) ? $cat_meta_data['cat_header'] : '',
                    'bottom_content' => isset($cat_meta_data['cat_footer']) ? $cat_meta_data['cat_footer'] : ''
                ];
            }

        }

        wp_localize_script('my-custom-plugin', '__yoastExtra', $data);
    }
}

function YoastAddCustomData() {
    new YoastAddCustomData();
}

if (!wp_installing()) {
    add_action('plugins_loaded', 'YoastAddCustomData', 20);
}
