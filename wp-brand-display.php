<?php
/**
 * Plugin Name: WP Brand Display
 * Description: Displays a brand image above the price on a single product page
 * Version: 1.0.0
 * Author: Bojan Pavlovic
 * License: GPL v2 or later
 * Text Domain: wp-brand-display
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class WP_Brand_Display {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
    }
    
    public function init() {
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }
        
        // Display brand image above price on single product page
        // Priority 5 to show before price (which is usually priority 10)
        add_action('woocommerce_single_product_summary', array($this, 'display_brand_image'), 5);
        
        // Add CSS styles
        add_action('wp_head', array($this, 'add_custom_styles'));
        
        // Add admin settings
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    public function woocommerce_missing_notice() {
        ?>
        <div class="notice notice-error">
            <p><?php _e('WP Brand Display zahteva WooCommerce plugin da bi radio.', 'wp-brand-display'); ?></p>
        </div>
        <?php
    }
    
    public function add_admin_menu() {
        add_options_page(
            'Brand Display Settings',
            'Brand Display',
            'manage_options',
            'wp-brand-display-settings',
            array($this, 'render_settings_page')
        );
    }
    
    public function register_settings() {
        register_setting('wp_brand_display_settings', 'wp_brand_display_taxonomy');
        register_setting('wp_brand_display_settings', 'wp_brand_display_image_size');
        register_setting('wp_brand_display_settings', 'wp_brand_display_max_width');
        register_setting('wp_brand_display_settings', 'wp_brand_display_link_to_brand');
    }
    
    public function add_custom_styles() {
        if (!is_product()) {
            return;
        }
        ?>
        <style>
            .wp-brand-display {
                margin-bottom: 20px;
                display: block;
            }
            .wp-brand-display img {
                max-width: 100%;
                height: auto;
                display: block;
            }
            .wp-brand-display a {
                display: inline-block;
                text-decoration: none;
            }
        </style>
        <?php
    }
    
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Handle form submission
        if (isset($_POST['wp_brand_display_submit']) && check_admin_referer('wp_brand_display_settings')) {
            update_option('wp_brand_display_taxonomy', sanitize_text_field($_POST['wp_brand_display_taxonomy']));
            update_option('wp_brand_display_image_size', sanitize_text_field($_POST['wp_brand_display_image_size']));
            update_option('wp_brand_display_max_width', sanitize_text_field($_POST['wp_brand_display_max_width']));
            update_option('wp_brand_display_link_to_brand', isset($_POST['wp_brand_display_link_to_brand']) ? '1' : '0');
            echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
        }
        
        $taxonomy = get_option('wp_brand_display_taxonomy', 'product_brand');
        $image_size = get_option('wp_brand_display_image_size', 'medium');
        $max_width = get_option('wp_brand_display_max_width', '200');
        $link_to_brand = get_option('wp_brand_display_link_to_brand', '0');
        
        // Get all product taxonomies
        $taxonomies = get_object_taxonomies('product', 'objects');
        $brand_taxonomies = array();
        foreach ($taxonomies as $tax) {
            if ($tax->public && $tax->show_ui) {
                $brand_taxonomies[$tax->name] = $tax->label;
            }
        }
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form method="post" action="">
                <?php wp_nonce_field('wp_brand_display_settings'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="wp_brand_display_taxonomy">Brand Taxonomy</label></th>
                        <td>
                            <select id="wp_brand_display_taxonomy" name="wp_brand_display_taxonomy">
                                <?php foreach ($brand_taxonomies as $tax_name => $tax_label): ?>
                                    <option value="<?php echo esc_attr($tax_name); ?>" <?php selected($taxonomy, $tax_name); ?>>
                                        <?php echo esc_html($tax_label . ' (' . $tax_name . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">Izaberite taxonomy koja se koristi za brand proizvoda (npr. product_brand, pa_brand, yith_product_brand)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="wp_brand_display_image_size">Image Size</label></th>
                        <td>
                            <select id="wp_brand_display_image_size" name="wp_brand_display_image_size">
                                <option value="thumbnail" <?php selected($image_size, 'thumbnail'); ?>>Thumbnail</option>
                                <option value="medium" <?php selected($image_size, 'medium'); ?>>Medium</option>
                                <option value="large" <?php selected($image_size, 'large'); ?>>Large</option>
                                <option value="full" <?php selected($image_size, 'full'); ?>>Full</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="wp_brand_display_max_width">Max Width (px)</label></th>
                        <td>
                            <input type="number" id="wp_brand_display_max_width" name="wp_brand_display_max_width" value="<?php echo esc_attr($max_width); ?>" class="small-text" />
                            <p class="description">Maksimalna širina slike brenda u pikselima</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Link to Brand Page</th>
                        <td>
                            <label>
                                <input type="checkbox" name="wp_brand_display_link_to_brand" value="1" <?php checked($link_to_brand, '1'); ?> />
                                Linkuj sliku brenda na stranicu brenda
                            </label>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button('Save Settings', 'primary', 'wp_brand_display_submit'); ?>
            </form>
        </div>
        <?php
    }
    
    public function display_brand_image() {
        global $product;
        
        if (!$product) {
            return;
        }
        
        // Get taxonomy from settings
        $taxonomy = get_option('wp_brand_display_taxonomy', 'product_brand');
        $image_size = get_option('wp_brand_display_image_size', 'medium');
        $max_width = get_option('wp_brand_display_max_width', '200');
        $link_to_brand = get_option('wp_brand_display_link_to_brand', '0');
        
        // Try to get brand from taxonomy
        $brand_terms = wp_get_post_terms($product->get_id(), $taxonomy, array('number' => 1));
        
        if (empty($brand_terms) || is_wp_error($brand_terms)) {
            // Try alternative taxonomies
            $alternative_taxonomies = array('pa_brand', 'yith_product_brand', 'product_brand', 'pwb-brand');
            foreach ($alternative_taxonomies as $alt_tax) {
                $brand_terms = wp_get_post_terms($product->get_id(), $alt_tax, array('number' => 1));
                if (!empty($brand_terms) && !is_wp_error($brand_terms)) {
                    $taxonomy = $alt_tax;
                    break;
                }
            }
        }
        
        if (empty($brand_terms) || is_wp_error($brand_terms)) {
            return;
        }
        
        $brand_term = $brand_terms[0];
        
        // Get brand image
        $brand_image_id = get_term_meta($brand_term->term_id, 'thumbnail_id', true);
        
        // Try alternative meta keys for brand image
        if (empty($brand_image_id)) {
            $alternative_keys = array('brand_image', 'image', 'term_image', 'brand_logo');
            foreach ($alternative_keys as $key) {
                $brand_image_id = get_term_meta($brand_term->term_id, $key, true);
                if (!empty($brand_image_id)) {
                    break;
                }
            }
        }
        
        // If still no image, try ACF field
        if (empty($brand_image_id) && function_exists('get_field')) {
            $brand_image_id = get_field('brand_image', $taxonomy . '_' . $brand_term->term_id);
            if (is_array($brand_image_id)) {
                $brand_image_id = $brand_image_id['ID'];
            }
        }
        
        if (empty($brand_image_id)) {
            return;
        }
        
        // Get image URL
        $image_url = wp_get_attachment_image_url($brand_image_id, $image_size);
        
        if (!$image_url) {
            return;
        }
        
        // Get image alt text
        $image_alt = get_post_meta($brand_image_id, '_wp_attachment_image_alt', true);
        if (empty($image_alt)) {
            $image_alt = $brand_term->name;
        }
        
        // Get brand page URL if linking is enabled
        $brand_url = '';
        if ($link_to_brand === '1') {
            $brand_url = get_term_link($brand_term->term_id, $taxonomy);
            if (is_wp_error($brand_url)) {
                $brand_url = '';
            }
        }
        
        // Display brand image
        ?>
        <div class="wp-brand-display" style="max-width: <?php echo esc_attr($max_width); ?>px;">
            <?php if (!empty($brand_url)): ?>
                <a href="<?php echo esc_url($brand_url); ?>" title="<?php echo esc_attr($brand_term->name); ?>">
                    <img 
                        src="<?php echo esc_url($image_url); ?>" 
                        alt="<?php echo esc_attr($image_alt); ?>"
                        style="max-width: 100%; height: auto; display: block;"
                        class="wp-brand-image"
                    />
                </a>
            <?php else: ?>
                <img 
                    src="<?php echo esc_url($image_url); ?>" 
                    alt="<?php echo esc_attr($image_alt); ?>"
                    style="max-width: 100%; height: auto; display: block;"
                    class="wp-brand-image"
                />
            <?php endif; ?>
        </div>
        <?php
    }
}

// Initialize the plugin
WP_Brand_Display::get_instance();
