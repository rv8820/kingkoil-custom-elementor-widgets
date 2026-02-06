<?php
/**
 * Plugin Name: KingKoil Custom Elementor Widgets
 * Description: Custom Elementor widgets for the KingKoil ecommerce site. Includes ACF Repeater widget and more.
 * Version: 1.0.0
 * Author: KingKoil
 * Text Domain: kingkoil-custom-elementor-widgets
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Require Elementor (free or Pro) and ACF (free or Pro) to be active.
add_action( 'plugins_loaded', function () {
    $missing = [];

    if ( ! did_action( 'elementor/loaded' ) ) {
        $missing[] = 'Elementor';
    }

    if ( ! class_exists( 'ACF' ) ) {
        $missing[] = 'Advanced Custom Fields';
    }

    if ( $missing ) {
        add_action( 'admin_notices', function () use ( $missing ) {
            printf(
                '<div class="notice notice-error"><p><strong>KingKoil Custom Elementor Widgets</strong> requires %s to be installed and active.</p></div>',
                esc_html( implode( ' and ', $missing ) )
            );
        } );
        return;
    }

    define( 'KINGKOIL_WIDGETS_VERSION', '1.0.0' );
    define( 'KINGKOIL_WIDGETS_PATH', plugin_dir_path( __FILE__ ) );
    define( 'KINGKOIL_WIDGETS_URL', plugin_dir_url( __FILE__ ) );

    require_once KINGKOIL_WIDGETS_PATH . 'includes/class-plugin.php';
    require_once KINGKOIL_WIDGETS_PATH . 'includes/dynamic-tags.php';

    \KingKoil\Elementor\Plugin::instance();
} );
