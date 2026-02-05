<?php
namespace KingKoil\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor widget that renders an ACF repeater field.
 *
 * Automatically detects all ACF repeater fields and their sub-fields.
 * Each repeater row is rendered as a fluid-width inline item using flexbox.
 * Handles image, text, and other common ACF field types.
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
        return [ 'acf', 'repeater', 'custom fields', 'list', 'highlight' ];
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

    /**
     * Query ACF for all repeater-type fields across every field group.
     */
    private function get_acf_repeater_fields() {
        $options = [ '' => __( '— Select a repeater field —', 'kingkoil-custom-elementor-widgets' ) ];

        if ( ! function_exists( 'acf_get_field_groups' ) || ! function_exists( 'acf_get_fields' ) ) {
            return $options;
        }

        $groups = acf_get_field_groups();

        foreach ( $groups as $group ) {
            $fields = acf_get_fields( $group['key'] );

            if ( ! $fields ) {
                continue;
            }

            foreach ( $fields as $field ) {
                if ( 'repeater' === $field['type'] ) {
                    $options[ $field['key'] ] = $field['label'];
                }
            }
        }

        return $options;
    }

    /**
     * Get the ACF field object for a given field key.
     */
    private function get_repeater_field_object( $field_key ) {
        if ( ! $field_key || ! function_exists( 'acf_get_field' ) ) {
            return null;
        }

        return acf_get_field( $field_key );
    }

    protected function register_controls() {

        /*--------------------------------------------------------------
         * Content tab — Repeater selection
         *------------------------------------------------------------*/
        $this->start_controls_section( 'section_repeater', [
            'label' => __( 'Repeater Field', 'kingkoil-custom-elementor-widgets' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'repeater_field', [
            'label'   => __( 'ACF Repeater Field', 'kingkoil-custom-elementor-widgets' ),
            'type'    => Controls_Manager::SELECT,
            'options' => $this->get_acf_repeater_fields(),
            'default' => '',
        ] );

        $this->add_control( 'empty_message', [
            'label'       => __( 'Empty Message', 'kingkoil-custom-elementor-widgets' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => '',
            'description' => __( 'Shown when the repeater has no rows. Leave blank to output nothing.', 'kingkoil-custom-elementor-widgets' ),
        ] );

        $this->end_controls_section();

        /*--------------------------------------------------------------
         * Style tab — Items Layout (the outer container)
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

        $this->add_responsive_control( 'items_gap', [
            'label'      => __( 'Gap Between Items', 'kingkoil-custom-elementor-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em', 'rem' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
            'default'    => [ 'size' => 12, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .kingkoil-acf-repeater' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'flex_wrap', [
            'label'        => __( 'Wrap Items', 'kingkoil-custom-elementor-widgets' ),
            'type'         => Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'return_value' => 'wrap',
            'selectors'    => [
                '{{WRAPPER}} .kingkoil-acf-repeater' => 'flex-wrap: {{VALUE}};',
            ],
        ] );

        $this->add_responsive_control( 'align_items', [
            'label'   => __( 'Vertical Alignment', 'kingkoil-custom-elementor-widgets' ),
            'type'    => Controls_Manager::CHOOSE,
            'options' => [
                'flex-start' => [ 'title' => __( 'Start', 'kingkoil-custom-elementor-widgets' ), 'icon' => 'eicon-align-start-v' ],
                'center'     => [ 'title' => __( 'Center', 'kingkoil-custom-elementor-widgets' ), 'icon' => 'eicon-align-center-v' ],
                'flex-end'   => [ 'title' => __( 'End', 'kingkoil-custom-elementor-widgets' ), 'icon' => 'eicon-align-end-v' ],
            ],
            'selectors' => [
                '{{WRAPPER}} .kingkoil-acf-repeater' => 'align-items: {{VALUE}};',
            ],
        ] );

        $this->add_responsive_control( 'justify_content', [
            'label'   => __( 'Horizontal Alignment', 'kingkoil-custom-elementor-widgets' ),
            'type'    => Controls_Manager::CHOOSE,
            'options' => [
                'flex-start' => [ 'title' => __( 'Start', 'kingkoil-custom-elementor-widgets' ), 'icon' => 'eicon-align-start-h' ],
                'center'     => [ 'title' => __( 'Center', 'kingkoil-custom-elementor-widgets' ), 'icon' => 'eicon-align-center-h' ],
                'flex-end'   => [ 'title' => __( 'End', 'kingkoil-custom-elementor-widgets' ), 'icon' => 'eicon-align-end-h' ],
            ],
            'selectors' => [
                '{{WRAPPER}} .kingkoil-acf-repeater' => 'justify-content: {{VALUE}};',
            ],
        ] );

        $this->end_controls_section();

        /*--------------------------------------------------------------
         * Style tab — Individual item
         *------------------------------------------------------------*/
        $this->start_controls_section( 'section_style_item', [
            'label' => __( 'Item', 'kingkoil-custom-elementor-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'item_direction', [
            'label'   => __( 'Content Direction', 'kingkoil-custom-elementor-widgets' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'column',
            'options' => [
                'column' => __( 'Stacked (image above text)', 'kingkoil-custom-elementor-widgets' ),
                'row'    => __( 'Inline (image beside text)', 'kingkoil-custom-elementor-widgets' ),
            ],
            'selectors' => [
                '{{WRAPPER}} .kingkoil-acf-repeater__item' => 'flex-direction: {{VALUE}};',
            ],
        ] );

        $this->add_responsive_control( 'item_inner_gap', [
            'label'      => __( 'Gap (image / text)', 'kingkoil-custom-elementor-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
            'default'    => [ 'size' => 8, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .kingkoil-acf-repeater__item' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'item_align', [
            'label'   => __( 'Align Contents', 'kingkoil-custom-elementor-widgets' ),
            'type'    => Controls_Manager::CHOOSE,
            'options' => [
                'flex-start' => [ 'title' => __( 'Start', 'kingkoil-custom-elementor-widgets' ), 'icon' => 'eicon-align-start-h' ],
                'center'     => [ 'title' => __( 'Center', 'kingkoil-custom-elementor-widgets' ), 'icon' => 'eicon-align-center-h' ],
                'flex-end'   => [ 'title' => __( 'End', 'kingkoil-custom-elementor-widgets' ), 'icon' => 'eicon-align-end-h' ],
            ],
            'selectors' => [
                '{{WRAPPER}} .kingkoil-acf-repeater__item' => 'align-items: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'item_bg_color', [
            'label'     => __( 'Background Color', 'kingkoil-custom-elementor-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .kingkoil-acf-repeater__item' => 'background-color: {{VALUE}};',
            ],
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

        $this->add_responsive_control( 'item_border_width', [
            'label'      => __( 'Border Width', 'kingkoil-custom-elementor-widgets' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .kingkoil-acf-repeater__item' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'item_border_color', [
            'label'     => __( 'Border Color', 'kingkoil-custom-elementor-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .kingkoil-acf-repeater__item' => 'border-color: {{VALUE}};',
            ],
        ] );

        $this->end_controls_section();

        /*--------------------------------------------------------------
         * Style tab — Image
         *------------------------------------------------------------*/
        $this->start_controls_section( 'section_style_image', [
            'label' => __( 'Image', 'kingkoil-custom-elementor-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'image_width', [
            'label'      => __( 'Width', 'kingkoil-custom-elementor-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%', 'em' ],
            'range'      => [
                'px' => [ 'min' => 10, 'max' => 500 ],
                '%'  => [ 'min' => 5, 'max' => 100 ],
            ],
            'selectors' => [
                '{{WRAPPER}} .kingkoil-acf-repeater__image' => 'width: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'image_height', [
            'label'      => __( 'Height', 'kingkoil-custom-elementor-widgets' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%', 'em' ],
            'range'      => [
                'px' => [ 'min' => 10, 'max' => 500 ],
            ],
            'selectors' => [
                '{{WRAPPER}} .kingkoil-acf-repeater__image' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'image_border_radius', [
            'label'      => __( 'Border Radius', 'kingkoil-custom-elementor-widgets' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [
                '{{WRAPPER}} .kingkoil-acf-repeater__image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'image_object_fit', [
            'label'   => __( 'Object Fit', 'kingkoil-custom-elementor-widgets' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'contain',
            'options' => [
                'contain' => 'Contain',
                'cover'   => 'Cover',
                'fill'    => 'Fill',
                'none'    => 'None',
            ],
            'selectors' => [
                '{{WRAPPER}} .kingkoil-acf-repeater__image' => 'object-fit: {{VALUE}};',
            ],
        ] );

        $this->end_controls_section();

        /*--------------------------------------------------------------
         * Style tab — Text
         *------------------------------------------------------------*/
        $this->start_controls_section( 'section_style_text', [
            'label' => __( 'Text', 'kingkoil-custom-elementor-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'text_color', [
            'label'     => __( 'Color', 'kingkoil-custom-elementor-widgets' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .kingkoil-acf-repeater__text' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'text_typography',
            'selector' => '{{WRAPPER}} .kingkoil-acf-repeater__text',
        ] );

        $this->add_responsive_control( 'text_align', [
            'label'   => __( 'Text Align', 'kingkoil-custom-elementor-widgets' ),
            'type'    => Controls_Manager::CHOOSE,
            'options' => [
                'left'   => [ 'title' => __( 'Left', 'kingkoil-custom-elementor-widgets' ), 'icon' => 'eicon-text-align-left' ],
                'center' => [ 'title' => __( 'Center', 'kingkoil-custom-elementor-widgets' ), 'icon' => 'eicon-text-align-center' ],
                'right'  => [ 'title' => __( 'Right', 'kingkoil-custom-elementor-widgets' ), 'icon' => 'eicon-text-align-right' ],
            ],
            'selectors' => [
                '{{WRAPPER}} .kingkoil-acf-repeater__text' => 'text-align: {{VALUE}};',
            ],
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings       = $this->get_settings_for_display();
        $field_key      = $settings['repeater_field'];
        $empty_message  = $settings['empty_message'];

        if ( empty( $field_key ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<p style="color:#999;font-style:italic;">';
                echo esc_html__( 'ACF Repeater — select a repeater field from the dropdown.', 'kingkoil-custom-elementor-widgets' );
                echo '</p>';
            }
            return;
        }

        // Resolve field object so we know the field name and sub-field types.
        $field_obj = $this->get_repeater_field_object( $field_key );

        if ( ! $field_obj ) {
            return;
        }

        $field_name     = $field_obj['name'];
        $sub_fields     = $field_obj['sub_fields'] ?? [];

        if ( ! have_rows( $field_name ) ) {
            if ( $empty_message ) {
                echo '<p>' . esc_html( $empty_message ) . '</p>';
            }
            return;
        }

        echo '<div class="kingkoil-acf-repeater">';

        while ( have_rows( $field_name ) ) : the_row();
            echo '<div class="kingkoil-acf-repeater__item">';

            foreach ( $sub_fields as $sub_field ) {
                $value = get_sub_field( $sub_field['name'] );

                if ( $value === false || $value === null || $value === '' ) {
                    continue;
                }

                switch ( $sub_field['type'] ) {
                    case 'image':
                        $this->render_image_field( $value );
                        break;

                    case 'wysiwyg':
                        echo '<div class="kingkoil-acf-repeater__text">' . wp_kses_post( $value ) . '</div>';
                        break;

                    case 'url':
                        echo '<a class="kingkoil-acf-repeater__text" href="' . esc_url( $value ) . '">' . esc_html( $value ) . '</a>';
                        break;

                    default:
                        echo '<span class="kingkoil-acf-repeater__text">' . esc_html( $value ) . '</span>';
                        break;
                }
            }

            echo '</div>';
        endwhile;

        echo '</div>';
    }

    /**
     * Render an ACF image sub-field value.
     *
     * Handles all three ACF return formats: array, URL string, and attachment ID.
     */
    private function render_image_field( $value ) {
        if ( is_array( $value ) ) {
            $url = $value['url'] ?? '';
            $alt = $value['alt'] ?? '';
            if ( $url ) {
                echo '<img class="kingkoil-acf-repeater__image" src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '">';
            }
        } elseif ( is_numeric( $value ) ) {
            echo wp_get_attachment_image( (int) $value, 'medium', false, [ 'class' => 'kingkoil-acf-repeater__image' ] );
        } else {
            echo '<img class="kingkoil-acf-repeater__image" src="' . esc_url( $value ) . '" alt="">';
        }
    }

    protected function content_template() {
        // Server-side rendered widget — no JS template.
    }
}
