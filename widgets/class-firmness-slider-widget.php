<?php
namespace KingKoil\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Firmness_Slider_Widget extends Widget_Base {

    private function get_firmness_levels() {
        return [
            'soft'        => __( 'Soft', 'kingkoil-custom-elementor-widgets' ),
            'medium-soft' => __( 'Medium Soft', 'kingkoil-custom-elementor-widgets' ),
            'medium'      => __( 'Medium', 'kingkoil-custom-elementor-widgets' ),
            'medium-firm' => __( 'Medium Firm', 'kingkoil-custom-elementor-widgets' ),
            'firm'        => __( 'Firm', 'kingkoil-custom-elementor-widgets' ),
            'extra-firm'  => __( 'Extra Firm', 'kingkoil-custom-elementor-widgets' ),
        ];
    }

    public function get_name() {
        return 'kingkoil_firmness_slider';
    }

    public function get_title() {
        return __( 'Firmness Slider', 'kingkoil-custom-elementor-widgets' );
    }

    public function get_icon() {
        return 'eicon-slider-device';
    }

    public function get_categories() {
        return [ 'kingkoil' ];
    }

    public function get_keywords() {
        return [ 'firmness', 'slider', 'mattress', 'soft', 'firm', 'woocommerce', 'product' ];
    }

    public function get_style_depends() {
        return [ 'kingkoil-firmness-slider' ];
    }

    public function __construct( $data = [], $args = null ) {
        parent::__construct( $data, $args );

        wp_register_style(
            'kingkoil-firmness-slider',
            KINGKOIL_WIDGETS_URL . 'assets/css/firmness-slider-widget.css',
            [],
            KINGKOIL_WIDGETS_VERSION
        );
    }

    protected function register_controls() {

        $this->start_controls_section( 'section_content', [
            'label' => __( 'Firmness', 'kingkoil-custom-elementor-widgets' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'no_firmness_behavior', [
            'label'   => __( 'If Firmness Is Not Set', 'kingkoil-custom-elementor-widgets' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'hide',
            'options' => [
                'hide'  => __( 'Hide widget', 'kingkoil-custom-elementor-widgets' ),
                'error' => __( 'Show notice', 'kingkoil-custom-elementor-widgets' ),
            ],
        ] );

        $this->add_control( 'no_firmness_message', [
            'label'     => __( 'Notice Text', 'kingkoil-custom-elementor-widgets' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Firmness information not available.', 'kingkoil-custom-elementor-widgets' ),
            'condition' => [
                'no_firmness_behavior' => 'error',
            ],
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'section_style_track', [
            'label' => __( 'Track', 'kingkoil-custom-elementor-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'track_height', [
            'label'      => __( 'Track Height', 'kingkoil-custom-elementor-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 4, 'max' => 24 ] ],
            'default'    => [ 'size' => 8, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .kk-firmness-slider__segment' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'track_gap', [
            'label'      => __( 'Gap Between Segments', 'kingkoil-custom-elementor-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 16 ] ],
            'default'    => [ 'size' => 6, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .kk-firmness-slider__segments' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'section_style_dot', [
            'label' => __( 'Indicator Dot', 'kingkoil-custom-elementor-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'dot_color', [
            'label'   => __( 'Dot Color', 'kingkoil-custom-elementor-widgets' ),
            'type'    => Controls_Manager::COLOR,
            'default' => '#1a6faa',
            'selectors' => [
                '{{WRAPPER}} .kk-firmness-slider__dot' => 'background-color: {{VALUE}};',
            ],
        ] );

        $this->add_responsive_control( 'dot_size', [
            'label'      => __( 'Dot Size', 'kingkoil-custom-elementor-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 10, 'max' => 40 ] ],
            'default'    => [ 'size' => 20, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .kk-firmness-slider__dot' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'section_style_title', [
            'label' => __( 'Current Firmness Title', 'kingkoil-custom-elementor-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'title_color', [
            'label'     => __( 'Color', 'kingkoil-custom-elementor-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .kk-firmness-slider__title' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'title_typography',
            'selector' => '{{WRAPPER}} .kk-firmness-slider__title',
        ] );

        $this->add_responsive_control( 'title_margin_bottom', [
            'label'      => __( 'Spacing Below Title', 'kingkoil-custom-elementor-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
            'default'    => [ 'size' => 8, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .kk-firmness-slider__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'section_style_labels', [
            'label' => __( 'Labels', 'kingkoil-custom-elementor-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'label_color', [
            'label'     => __( 'Color', 'kingkoil-custom-elementor-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .kk-firmness-slider__label' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'label_typography',
            'selector' => '{{WRAPPER}} .kk-firmness-slider__label',
        ] );

        $this->add_responsive_control( 'labels_margin_top', [
            'label'      => __( 'Spacing Above Labels', 'kingkoil-custom-elementor-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
            'default'    => [ 'size' => 10, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .kk-firmness-slider__labels' => 'margin-top: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();
    }

    // -----------------------------------------------------------------------
    // Helpers — static so they can be called from render_for_product()
    // without instantiating Widget_Base outside of Elementor context.
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Render
    // -----------------------------------------------------------------------

    protected function render() {
        $settings = $this->get_settings_for_display();
        $levels   = $this->get_firmness_levels();
        $keys     = array_keys( $levels );
        $total    = count( $keys );

        $product_id   = get_the_ID();
        $terms        = get_the_terms( $product_id, 'pa_firmness' );
        $current_slug = null;

        if ( $terms && ! is_wp_error( $terms ) ) {
            $current_slug = $terms[0]->slug;
        }

        $index = ( $current_slug !== null ) ? array_search( $current_slug, $keys, true ) : false;

        if ( $index === false ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div class="kk-firmness-slider kk-firmness-slider--placeholder">';
                echo '<p style="color:#999;font-style:italic;">';
                printf(
                    esc_html__( 'Firmness Slider — no "%s" attribute found. Preview uses "Medium".', 'kingkoil-custom-elementor-widgets' ),
                    'pa_firmness'
                );
                echo '</p>';
                $this->render_slider( $settings, $keys, $levels, $total, 2 );
                echo '</div>';
                return;
            }

            if ( 'error' === $settings['no_firmness_behavior'] ) {
                $msg = $settings['no_firmness_message'] ?: __( 'Firmness information not available.', 'kingkoil-custom-elementor-widgets' );
                echo '<p class="kk-firmness-error">' . esc_html( $msg ) . '</p>';
            }
            return;
        }

        $this->render_slider( $settings, $keys, $levels, $total, $index );
    }

    /**
     * Output the slider HTML.
     * Called both by render() (instance context) and render_for_product() (static context).
     */
    public function render_slider( $settings, $keys, $levels, $total, $index ) {
        $dot_pct       = ( $total > 0 ) ? ( ( $index + 0.5 ) / $total * 100 ) : 50;
        $current_label = isset( $levels[ $keys[ $index ] ] ) ? $levels[ $keys[ $index ] ] : '';
        ?>
        <div class="kk-firmness-slider" role="img" aria-label="<?php echo esc_attr( sprintf( __( 'Firmness: %s', 'kingkoil-custom-elementor-widgets' ), $current_label ) ); ?>">

            <div class="kk-firmness-slider__title"><?php echo esc_html( $current_label ); ?></div>

            <div class="kk-firmness-slider__track-wrap">

                <div class="kk-firmness-slider__segments">
                    <?php for ( $i = 0; $i < $total; $i++ ) : ?>
                        <span class="kk-firmness-slider__segment"></span>
                    <?php endfor; ?>
                </div>

                <span
                    class="kk-firmness-slider__dot"
                    style="left: <?php echo esc_attr( number_format( $dot_pct, 4 ) ); ?>%;"
                    aria-hidden="true"
                ></span>

            </div><!-- .kk-firmness-slider__track-wrap -->

            <div class="kk-firmness-slider__labels" aria-hidden="true">
                <span class="kk-firmness-slider__label kk-firmness-slider__label--start">
                    <?php esc_html_e( 'Soft', 'kingkoil-custom-elementor-widgets' ); ?>
                </span>
                <span class="kk-firmness-slider__label kk-firmness-slider__label--mid">
                    <?php esc_html_e( 'Medium', 'kingkoil-custom-elementor-widgets' ); ?>
                </span>
                <span class="kk-firmness-slider__label kk-firmness-slider__label--end">
                    <?php esc_html_e( 'Extra Firm', 'kingkoil-custom-elementor-widgets' ); ?>
                </span>
            </div>

        </div><!-- .kk-firmness-slider -->
        <?php
    }

    protected function content_template() {
        // Server-side rendered widget — no JS preview template.
    }

    /**
     * Static helper so external code (e.g. YITH compare) can reuse
     * the same HTML without instantiating Widget_Base outside Elementor.
     *
     * @param int   $product_id
     * @param array $settings   Optional — pass custom colors or leave empty for defaults.
     * @return string           HTML string, empty string if firmness not set.
     */
    public static function render_for_product( $product_id, $settings = [] ) {
        $defaults = [
            'no_firmness_behavior' => 'hide',
            'no_firmness_message'  => 'Firmness information not available.',
        ];
        $settings = wp_parse_args( $settings, $defaults );

        $levels = [
            'soft'        => __( 'Soft', 'kingkoil-custom-elementor-widgets' ),
            'medium-soft' => __( 'Medium Soft', 'kingkoil-custom-elementor-widgets' ),
            'medium'      => __( 'Medium', 'kingkoil-custom-elementor-widgets' ),
            'medium-firm' => __( 'Medium Firm', 'kingkoil-custom-elementor-widgets' ),
            'firm'        => __( 'Firm', 'kingkoil-custom-elementor-widgets' ),
            'extra-firm'  => __( 'Extra Firm', 'kingkoil-custom-elementor-widgets' ),
        ];

        $keys  = array_keys( $levels );
        $total = count( $keys );

        $terms        = get_the_terms( $product_id, 'pa_firmness' );
        $current_slug = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->slug : null;
        $index        = ( $current_slug !== null ) ? array_search( $current_slug, $keys, true ) : false;

        if ( $index === false ) {
            return '';
        }

        $dot_pct       = ( $total > 0 ) ? ( ( $index + 0.5 ) / $total * 100 ) : 50;
        $current_label = isset( $levels[ $keys[ $index ] ] ) ? $levels[ $keys[ $index ] ] : '';

        ob_start();
        ?>
        <div class="kk-firmness-slider" role="img" aria-label="<?php echo esc_attr( sprintf( __( 'Firmness: %s', 'kingkoil-custom-elementor-widgets' ), $current_label ) ); ?>">

            <div class="kk-firmness-slider__title"><?php echo esc_html( $current_label ); ?></div>

            <div class="kk-firmness-slider__track-wrap">

                <div class="kk-firmness-slider__segments">
                    <?php for ( $i = 0; $i < $total; $i++ ) : ?>
                        <span class="kk-firmness-slider__segment"></span>
                    <?php endfor; ?>
                </div>

                <span
                    class="kk-firmness-slider__dot"
                    style="left: <?php echo esc_attr( number_format( $dot_pct, 4 ) ); ?>%;"
                    aria-hidden="true"
                ></span>

            </div><!-- .kk-firmness-slider__track-wrap -->

            <div class="kk-firmness-slider__labels" aria-hidden="true">
                <span class="kk-firmness-slider__label kk-firmness-slider__label--start">
                    <?php esc_html_e( 'Soft', 'kingkoil-custom-elementor-widgets' ); ?>
                </span>
                <span class="kk-firmness-slider__label kk-firmness-slider__label--mid">
                    <?php esc_html_e( 'Medium', 'kingkoil-custom-elementor-widgets' ); ?>
                </span>
                <span class="kk-firmness-slider__label kk-firmness-slider__label--end">
                    <?php esc_html_e( 'Extra Firm', 'kingkoil-custom-elementor-widgets' ); ?>
                </span>
            </div>

        </div><!-- .kk-firmness-slider -->
        <?php
        return ob_get_clean();
    }
}