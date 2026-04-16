<?php
namespace KingKoil\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Product_Weight_Widget extends Widget_Base {

    public function get_name() {
        return 'kingkoil_product_weight';
    }

    public function get_title() {
        return __( 'Product Weight', 'kingkoil-custom-elementor-widgets' );
    }

    public function get_icon() {
        return 'eicon-product-info';
    }

    public function get_categories() {
        return [ 'kingkoil' ];
    }

    public function get_keywords() {
        return [ 'weight', 'product', 'woocommerce', 'variation' ];
    }

    protected function register_controls() {

        $this->start_controls_section( 'section_content', [
            'label' => __( 'Weight', 'kingkoil-custom-elementor-widgets' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'label', [
            'label'   => __( 'Label', 'kingkoil-custom-elementor-widgets' ),
            'type'    => Controls_Manager::TEXT,
            'default' => __( 'Weight', 'kingkoil-custom-elementor-widgets' ),
        ] );

        $this->add_control( 'unit', [
            'label'   => __( 'Unit', 'kingkoil-custom-elementor-widgets' ),
            'type'    => Controls_Manager::TEXT,
            'default' => 'kg',
        ] );

        $this->add_control( 'no_weight_behavior', [
            'label'   => __( 'If Weight Is Not Set', 'kingkoil-custom-elementor-widgets' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'hide',
            'options' => [
                'hide'  => __( 'Hide widget', 'kingkoil-custom-elementor-widgets' ),
                'error' => __( 'Show notice', 'kingkoil-custom-elementor-widgets' ),
            ],
        ] );

        $this->add_control( 'no_weight_message', [
            'label'     => __( 'Notice Text', 'kingkoil-custom-elementor-widgets' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Weight information not available.', 'kingkoil-custom-elementor-widgets' ),
            'condition' => [
                'no_weight_behavior' => 'error',
            ],
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'section_style', [
            'label' => __( 'Style', 'kingkoil-custom-elementor-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'text_color', [
            'label'     => __( 'Color', 'kingkoil-custom-elementor-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .kk-product-weight' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'text_typography',
            'selector' => '{{WRAPPER}} .kk-product-weight',
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings   = $this->get_settings_for_display();
        $product_id = get_the_ID();
        $output     = self::get_weight_string( $product_id, $settings['unit'] );

        if ( $output === '' ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                $label = $settings['label'] ?: 'Weight';
                echo '<p class="kk-product-weight"><strong>' . esc_html( $label ) . ':</strong> 0.69kg <em style="color:#999;">(preview)</em></p>';
                return;
            }

            if ( 'error' === $settings['no_weight_behavior'] ) {
                $msg = $settings['no_weight_message'] ?: __( 'Weight information not available.', 'kingkoil-custom-elementor-widgets' );
                echo '<p class="kk-product-weight-error">' . esc_html( $msg ) . '</p>';
            }
            return;
        }

        $label = $settings['label'] ?: 'Weight';
        echo '<p class="kk-product-weight"><strong>' . esc_html( $label ) . ':</strong> ' . esc_html( $output ) . '</p>';
    }

    protected function content_template() {
        // Server-side rendered widget — no JS preview template.
    }

    /**
     * Get a formatted weight string for a product.
     * For variable products, collects all unique variation weights.
     *
     * @param int    $product_id
     * @param string $unit       Unit suffix, e.g. "kg".
     * @return string            e.g. "0.69kg" or "0.69kg, 0.79kg, 1.09kg", empty string if none.
     */
    public static function get_weight_string( $product_id, $unit = 'kg' ) {
        $product = wc_get_product( $product_id );

        if ( ! $product ) {
            return '';
        }

        $weights = [];

        if ( $product->is_type( 'variable' ) ) {
            foreach ( $product->get_available_variations() as $variation ) {
                $w = $variation['weight'] ?? '';
                if ( $w !== '' && $w !== null ) {
                    $weights[] = (float) $w;
                }
            }
            $weights = array_unique( $weights );
            sort( $weights );
        } else {
            $w = $product->get_weight();
            if ( $w !== '' && $w !== null ) {
                $weights[] = (float) $w;
            }
        }

        if ( empty( $weights ) ) {
            return '';
        }

        $unit = $unit ?: 'kg';

        return implode( ', ', array_map( function( $w ) use ( $unit ) {
            return $w . $unit;
        }, $weights ) );
    }

    /**
     * Static helper for YITH compare table or other external contexts.
     *
     * @param int    $product_id
     * @param string $label
     * @param string $unit
     * @return string HTML string, empty string if weight not set.
     */
    public static function render_for_product( $product_id, $label = 'Weight', $unit = 'kg' ) {
        $output = self::get_weight_string( $product_id, $unit );

        if ( $output === '' ) {
            return '';
        }

        return '<p class="kk-product-weight"><strong>' . esc_html( $label ) . ':</strong> ' . esc_html( $output ) . '</p>';
    }
}
