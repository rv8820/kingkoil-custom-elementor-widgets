<?php
namespace KingKoil\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main plugin loader.
 *
 * Registers custom widget categories and widgets with Elementor.
 * New widgets should be added to the register_widgets() method.
 */
final class Plugin {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
        add_action( 'elementor/elements/categories_registered', [ $this, 'register_categories' ] );
    }

    /**
     * Register a custom widget category so all KingKoil widgets are grouped together.
     */
    public function register_categories( $elements_manager ) {
        $elements_manager->add_category( 'kingkoil', [
            'title' => __( 'KingKoil', 'kingkoil-custom-elementor-widgets' ),
            'icon'  => 'fa fa-plug',
        ] );
    }

    /**
     * Register all custom widgets.
     *
     * To add a new widget:
     * 1. Create a class in the widgets/ directory.
     * 2. Require the file and register the widget here.
     */
    public function register_widgets( $widgets_manager ) {
        require_once KINGKOIL_WIDGETS_PATH . 'widgets/class-acf-repeater-widget.php';
        $widgets_manager->register( new Widgets\ACF_Repeater_Widget() );

        require_once KINGKOIL_WIDGETS_PATH . 'widgets/class-firmness-slider-widget.php';
        $widgets_manager->register( new Widgets\Firmness_Slider_Widget() );

        require_once KINGKOIL_WIDGETS_PATH . 'widgets/class-product-weight-widget.php';
        $widgets_manager->register( new Widgets\Product_Weight_Widget() );
    }
}
