<?php
namespace KingKoil\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor widget that renders an ACF repeater field.
 *
 * The user configures the repeater field name and its sub-fields via the
 * Elementor editor. Each repeater row is rendered as an inline, fluid-width
 * item — no fixed grid.
 */
class ACF_Repeater_Widget extends Widget_Base {

    public function get_name() {
        return 'kingkoil_acf_repeater';
    }

    public function get_title() {
        return __( 'ACF Repeater', 'kingkoil-custom-elementor-widgets' );
    }

    public function get_icon() {
        return 'eicon-bullet-list';
    }

    public function get_categories() {
        return [ 'kingkoil' ];
    }

    public function get_keywords() {
        return [ 'acf', 'repeater', 'custom fields', 'list' ];
    }

    public function get_style_depends() {
        return [ 'kingkoil-acf-repeater' ];

    }

    public function __construct( $data = [], $args = null ) {
        parent::__construct( $data, $args );

        wp_register_style(
            'kingkoil-acf-repeater',
            KINGKOIL_WIDGETS_URL . 'assets/css/acf-repeater-widget.css',
            [],
            KINGKOIL_WIDGETS_VERSION
        );
    }

    protected function register_controls() {

        /*--------------------------------------------------------------
         * Content tab — Repeater configuration
         *------------------------------------------------------------*/
        $this->start_controls_section( 'section_repeater', [
            'label' => __( 'Repeater Field', 'kingkoil-custom-elementor-widgets' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'repeater_field_name', [
            'label'       => __( 'Repeater Field Name', 'kingkoil-custom-elementor-widgets' ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => 'numbers',
            'description' => __( 'Enter the ACF repeater field name exactly as defined in ACF.', 'kingkoil-custom-elementor-widgets' ),
        ] );

        $sub_fields = new Repeater();

        $sub_fields->add_control( 'sub_field_name', [
            'label'       => __( 'Sub Field Name', 'kingkoil-custom-elementor-widgets' ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => 'digit',
            'description' => __( 'ACF sub-field name.', 'kingkoil-custom-elementor-widgets' ),
        ] );

        $sub_fields->add_control( 'sub_field_label', [
            'label'       => __( 'Label (optional)', 'kingkoil-custom-elementor-widgets' ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => 'Digit',
            'description' => __( 'Display label shown before the value. Leave empty to show value only.', 'kingkoil-custom-elementor-widgets' ),
        ] );

        $this->add_control( 'sub_fields', [
            'label'       => __( 'Sub Fields', 'kingkoil-custom-elementor-widgets' ),
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $sub_fields->get_controls(),
            'title_field' => '{{{ sub_field_name }}}',
        ] );

        $this->add_control( 'separator', [
            'label'       => __( 'Separator', 'kingkoil-custom-elementor-widgets' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => '',
            'description' => __( 'Optional text/character between sub-field values within a single repeater row (e.g. " - " or " | ").', 'kingkoil-custom-elementor-widgets' ),
        ] );

        $this->add_control( 'empty_message', [
            'label'   => __( 'Empty Message', 'kingkoil-custom-elementor-widgets' ),
            'type'    => Controls_Manager::TEXT,
            'default' => '',
            'description' => __( 'Text shown when the repeater has no rows. Leave blank to output nothing.', 'kingkoil-custom-elementor-widgets' ),
        ] );

        $this->add_control( 'html_tag', [
            'label'   => __( 'HTML Tag', 'kingkoil-custom-elementor-widgets' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'span',
            'options' => [
                'span' => 'span',
                'div'  => 'div',
                'li'   => 'li',
                'p'    => 'p',
            ],
            'description' => __( 'Wrapper tag for each repeater item.', 'kingkoil-custom-elementor-widgets' ),
        ] );

        $this->add_control( 'wrapper_tag', [
            'label'   => __( 'Wrapper Tag', 'kingkoil-custom-elementor-widgets' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'div',
            'options' => [
                'div' => 'div',
                'ul'  => 'ul',
                'ol'  => 'ol',
            ],
            'description' => __( 'Outer wrapper tag. Use ul/ol when item tag is li.', 'kingkoil-custom-elementor-widgets' ),
        ] );

        $this->end_controls_section();

        /*--------------------------------------------------------------
         * Style tab — Layout
         *------------------------------------------------------------*/
        $this->start_controls_section( 'section_style_layout', [
            'label' => __( 'Layout', 'kingkoil-custom-elementor-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'layout_direction', [
            'label'   => __( 'Direction', 'kingkoil-custom-elementor-widgets' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'row',
            'options' => [
                'row'    => __( 'Horizontal', 'kingkoil-custom-elementor-widgets' ),
                'column' => __( 'Vertical', 'kingkoil-custom-elementor-widgets' ),
            ],
            'selectors' => [
                '{{WRAPPER}} .kingkoil-acf-repeater' => 'flex-direction: {{VALUE}};',
            ],
        ] );

        $this->add_responsive_control( 'item_gap', [
            'label'      => __( 'Gap Between Items', 'kingkoil-custom-elementor-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em', 'rem' ],
            'range'      => [
                'px' => [ 'min' => 0, 'max' => 100 ],
            ],
            'default'    => [ 'size' => 8, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .kingkoil-acf-repeater' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'flex_wrap', [
            'label'   => __( 'Wrap Items', 'kingkoil-custom-elementor-widgets' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'selectors' => [
                '{{WRAPPER}} .kingkoil-acf-repeater' => 'flex-wrap: {{VALUE}};',
            ],
            'return_value' => 'wrap',
        ] );

        $this->add_responsive_control( 'align_items', [
            'label'   => __( 'Vertical Alignment', 'kingkoil-custom-elementor-widgets' ),
            'type'    => Controls_Manager::CHOOSE,
            'options' => [
                'flex-start' => [
                    'title' => __( 'Start', 'kingkoil-custom-elementor-widgets' ),
                    'icon'  => 'eicon-align-start-v',
                ],
                'center' => [
                    'title' => __( 'Center', 'kingkoil-custom-elementor-widgets' ),
                    'icon'  => 'eicon-align-center-v',
                ],
                'flex-end' => [
                    'title' => __( 'End', 'kingkoil-custom-elementor-widgets' ),
                    'icon'  => 'eicon-align-end-v',
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .kingkoil-acf-repeater' => 'align-items: {{VALUE}};',
            ],
        ] );

        $this->add_responsive_control( 'justify_content', [
            'label'   => __( 'Horizontal Alignment', 'kingkoil-custom-elementor-widgets' ),
            'type'    => Controls_Manager::CHOOSE,
            'options' => [
                'flex-start' => [
                    'title' => __( 'Start', 'kingkoil-custom-elementor-widgets' ),
                    'icon'  => 'eicon-align-start-h',
                ],
                'center' => [
                    'title' => __( 'Center', 'kingkoil-custom-elementor-widgets' ),
                    'icon'  => 'eicon-align-center-h',
                ],
                'flex-end' => [
                    'title' => __( 'End', 'kingkoil-custom-elementor-widgets' ),
                    'icon'  => 'eicon-align-end-h',
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .kingkoil-acf-repeater' => 'justify-content: {{VALUE}};',
            ],
        ] );

        $this->end_controls_section();

        /*--------------------------------------------------------------
         * Style tab — Item styling
         *------------------------------------------------------------*/
        $this->start_controls_section( 'section_style_item', [
            'label' => __( 'Item', 'kingkoil-custom-elementor-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'item_color', [
            'label'     => __( 'Text Color', 'kingkoil-custom-elementor-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .kingkoil-acf-repeater__item' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'item_bg_color', [
            'label'     => __( 'Background Color', 'kingkoil-custom-elementor-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .kingkoil-acf-repeater__item' => 'background-color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'item_typography',
            'selector' => '{{WRAPPER}} .kingkoil-acf-repeater__item',
        ] );

        $this->add_responsive_control( 'item_padding', [
            'label'      => __( 'Padding', 'kingkoil-custom-elementor-widgets' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors'  => [
                '{{WRAPPER}} .kingkoil-acf-repeater__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'item_border_radius', [
            'label'      => __( 'Border Radius', 'kingkoil-custom-elementor-widgets' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [
                '{{WRAPPER}} .kingkoil-acf-repeater__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'item_border_color', [
            'label'     => __( 'Border Color', 'kingkoil-custom-elementor-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .kingkoil-acf-repeater__item' => 'border-color: {{VALUE}};',
            ],
            'condition' => [
                'item_border_width[top]!' => '',
            ],
        ] );

        $this->add_responsive_control( 'item_border_width', [
            'label'      => __( 'Border Width', 'kingkoil-custom-elementor-widgets' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .kingkoil-acf-repeater__item' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();

        /*--------------------------------------------------------------
         * Style tab — Label styling
         *------------------------------------------------------------*/
        $this->start_controls_section( 'section_style_label', [
            'label' => __( 'Label', 'kingkoil-custom-elementor-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'label_color', [
            'label'     => __( 'Label Color', 'kingkoil-custom-elementor-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .kingkoil-acf-repeater__label' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'label_typography',
            'selector' => '{{WRAPPER}} .kingkoil-acf-repeater__label',
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings           = $this->get_settings_for_display();
        $repeater_field     = $settings['repeater_field_name'];
        $sub_fields         = $settings['sub_fields'];
        $separator          = $settings['separator'];
        $empty_message      = $settings['empty_message'];
        $item_tag           = $settings['html_tag'];
        $wrapper_tag        = $settings['wrapper_tag'];
        $allowed_tags       = [ 'div', 'ul', 'ol', 'span', 'li', 'p' ];

        if ( ! in_array( $item_tag, $allowed_tags, true ) ) {
            $item_tag = 'span';
        }
        if ( ! in_array( $wrapper_tag, $allowed_tags, true ) ) {
            $wrapper_tag = 'div';
        }

        if ( empty( $repeater_field ) || empty( $sub_fields ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<p style="color:#999;font-style:italic;">';
                echo esc_html__( 'ACF Repeater — enter a repeater field name and at least one sub-field.', 'kingkoil-custom-elementor-widgets' );
                echo '</p>';
            }
            return;
        }

        if ( ! have_rows( $repeater_field ) ) {
            if ( $empty_message ) {
                echo '<p>' . esc_html( $empty_message ) . '</p>';
            }
            return;
        }

        echo '<' . esc_attr( $wrapper_tag ) . ' class="kingkoil-acf-repeater">';

        while ( have_rows( $repeater_field ) ) : the_row();
            echo '<' . esc_attr( $item_tag ) . ' class="kingkoil-acf-repeater__item">';

            $parts = [];

            foreach ( $sub_fields as $field ) {
                $name  = $field['sub_field_name'] ?? '';
                $label = $field['sub_field_label'] ?? '';

                if ( empty( $name ) ) {
                    continue;
                }

                $value = get_sub_field( $name );

                if ( $value === false || $value === null || $value === '' ) {
                    continue;
                }

                $html = '';
                if ( $label ) {
                    $html .= '<span class="kingkoil-acf-repeater__label">' . esc_html( $label ) . '</span> ';
                }
                $html .= '<span class="kingkoil-acf-repeater__value">' . esc_html( $value ) . '</span>';

                $parts[] = $html;
            }

            echo implode( esc_html( $separator ), $parts );

            echo '</' . esc_attr( $item_tag ) . '>';
        endwhile;

        echo '</' . esc_attr( $wrapper_tag ) . '>';
    }

    protected function content_template() {
        // Server-side rendered widget — no JS template needed.
    }
}
