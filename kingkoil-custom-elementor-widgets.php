<?php
/**
 * Plugin Name: KingKoil Custom Elementor Widgets
 * Description: Custom Elementor widgets for the KingKoil ecommerce site. Includes ACF Repeater widget and more.
 * Version: 1.0.0
 * Author: KingKoil
 * Text Domain: kingkoil-custom-elementor-widgets
 * Requires Plugins: elementor, advanced-custom-fields
 * Elementor tested up to: 3.28
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'KINGKOIL_WIDGETS_VERSION', '1.0.0' );
define( 'KINGKOIL_WIDGETS_PATH', plugin_dir_path( __FILE__ ) );
define( 'KINGKOIL_WIDGETS_URL', plugin_dir_url( __FILE__ ) );

require_once KINGKOIL_WIDGETS_PATH . 'includes/class-plugin.php';

\KingKoil\Elementor\Plugin::instance();
