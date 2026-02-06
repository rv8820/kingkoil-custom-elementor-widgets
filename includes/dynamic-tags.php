<?php
/**
 * ACF Repeater Sub-field Dynamic Tags for Elementor.
 *
 * These dynamic tags allow pulling ACF sub-field values directly into
 * any Elementor widget that supports dynamic content. They only return
 * values when used inside an ACF repeater loop context (i.e., within a
 * template rendered by the ACF Repeater widget).
 */

namespace KingKoil\Elementor\DynamicTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as DynamicTagsModule;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register all dynamic tags.
 */
function register_tags( $dynamic_tags_manager ) {
    // Register the group first.
    $dynamic_tags_manager->register_group(
        'acf-repeater',
        [
            'title' => __( 'ACF Repeater', 'kingkoil-custom-elementor-widgets' ),
        ]
    );

    // Register tags.
    $dynamic_tags_manager->register( new ACF_Sub_Field_Tag() );
    $dynamic_tags_manager->register( new ACF_Sub_Field_Image_Tag() );
    $dynamic_tags_manager->register( new ACF_Sub_Field_URL_Tag() );
}
add_action( 'elementor/dynamic_tags/register', __NAMESPACE__ . '\\register_tags' );


/**
 * Dynamic tag for ACF text/number/textarea sub-fields.
 */
class ACF_Sub_Field_Tag extends Tag {

    public function get_name() {
        return 'acf-sub-field';
    }

    public function get_title() {
        return __( 'ACF Sub-field', 'kingkoil-custom-elementor-widgets' );
    }

    public function get_group() {
        return 'acf-repeater';
    }

    public function get_categories() {
        return [
            DynamicTagsModule::TEXT_CATEGORY,
            DynamicTagsModule::NUMBER_CATEGORY,
            DynamicTagsModule::POST_META_CATEGORY,
        ];
    }

    protected function register_controls() {
        $this->add_control(
            'field_name',
            [
                'label'       => __( 'Sub-field Name', 'kingkoil-custom-elementor-widgets' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => 'title',
                'description' => __( 'Enter the ACF sub-field name.', 'kingkoil-custom-elementor-widgets' ),
            ]
        );
    }

    public function render() {
        $field_name = $this->get_settings( 'field_name' );

        if ( empty( $field_name ) ) {
            return;
        }

        $value = get_sub_field( $field_name );

        if ( $value === false || $value === null ) {
            return;
        }

        echo wp_kses_post( $value );
    }
}


/**
 * Dynamic tag for ACF image sub-fields.
 *
 * Returns image data in the format Elementor expects for image controls.
 */
class ACF_Sub_Field_Image_Tag extends Data_Tag {

    public function get_name() {
        return 'acf-sub-field-image';
    }

    public function get_title() {
        return __( 'ACF Sub-field Image', 'kingkoil-custom-elementor-widgets' );
    }

    public function get_group() {
        return 'acf-repeater';
    }

    public function get_categories() {
        return [ DynamicTagsModule::IMAGE_CATEGORY ];
    }

    protected function register_controls() {
        $this->add_control(
            'field_name',
            [
                'label'       => __( 'Sub-field Name', 'kingkoil-custom-elementor-widgets' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => 'image',
                'description' => __( 'Enter the ACF image sub-field name.', 'kingkoil-custom-elementor-widgets' ),
            ]
        );

        $this->add_control(
            'fallback',
            [
                'label' => __( 'Fallback', 'kingkoil-custom-elementor-widgets' ),
                'type'  => Controls_Manager::MEDIA,
            ]
        );
    }

    public function get_value( array $options = [] ) {
        $field_name = $this->get_settings( 'field_name' );
        $fallback   = $this->get_settings( 'fallback' );

        if ( empty( $field_name ) ) {
            return $fallback;
        }

        $value = get_sub_field( $field_name );

        if ( empty( $value ) ) {
            return $fallback;
        }

        // Handle different ACF return formats.
        if ( is_array( $value ) ) {
            return [
                'id'  => $value['ID'] ?? 0,
                'url' => $value['url'] ?? '',
            ];
        }

        if ( is_numeric( $value ) ) {
            return [
                'id'  => (int) $value,
                'url' => wp_get_attachment_url( (int) $value ) ?: '',
            ];
        }

        // Assume URL string.
        return [
            'id'  => 0,
            'url' => $value,
        ];
    }
}


/**
 * Dynamic tag for ACF URL sub-fields.
 *
 * Can be used in link fields, button URLs, etc.
 */
class ACF_Sub_Field_URL_Tag extends Data_Tag {

    public function get_name() {
        return 'acf-sub-field-url';
    }

    public function get_title() {
        return __( 'ACF Sub-field URL', 'kingkoil-custom-elementor-widgets' );
    }

    public function get_group() {
        return 'acf-repeater';
    }

    public function get_categories() {
        return [ DynamicTagsModule::URL_CATEGORY ];
    }

    protected function register_controls() {
        $this->add_control(
            'field_name',
            [
                'label'       => __( 'Sub-field Name', 'kingkoil-custom-elementor-widgets' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => 'link',
                'description' => __( 'Enter the ACF URL sub-field name.', 'kingkoil-custom-elementor-widgets' ),
            ]
        );
    }

    public function get_value( array $options = [] ) {
        $field_name = $this->get_settings( 'field_name' );

        if ( empty( $field_name ) ) {
            return '';
        }

        $value = get_sub_field( $field_name );

        if ( empty( $value ) ) {
            return '';
        }

        // Handle ACF link field (returns array) or plain URL.
        if ( is_array( $value ) && isset( $value['url'] ) ) {
            return $value['url'];
        }

        return $value;
    }
}
