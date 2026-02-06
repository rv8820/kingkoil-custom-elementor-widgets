<?php
/**
 * Shortcodes for use in Elementor templates rendered by the ACF Repeater widget.
 *
 * These shortcodes only return values when used inside an ACF repeater loop
 * context (i.e., within a template rendered by the ACF Repeater widget).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * [acf_sub] — Output an ACF sub-field value from the current repeater row.
 *
 * Usage:
 *   [acf_sub name="title"]                     — text/number fields
 *   [acf_sub name="icon" type="image"]         — image field (outputs <img>)
 *   [acf_sub name="icon" type="image_url"]     — image field (outputs just the URL)
 *   [acf_sub name="link" type="url"]           — URL field (outputs <a> link)
 *
 * Attributes:
 *   name   (required) — The ACF sub-field name.
 *   type   (optional) — Field type hint: "image", "image_url", "url". Default: text.
 *   size   (optional) — Image size for image fields. Default: "medium".
 *   class  (optional) — CSS class to add to the output element.
 *   link_text (optional) — Text for URL links. Defaults to the URL itself.
 */
add_shortcode( 'acf_sub', function ( $atts ) {
    $atts = shortcode_atts( [
        'name'      => '',
        'type'      => 'text',
        'size'      => 'medium',
        'class'     => '',
        'link_text' => '',
    ], $atts, 'acf_sub' );

    $name = sanitize_text_field( $atts['name'] );

    if ( empty( $name ) ) {
        return '';
    }

    $value = get_sub_field( $name );

    if ( $value === false || $value === null || $value === '' ) {
        return '';
    }

    $class_attr = $atts['class'] ? ' class="' . esc_attr( $atts['class'] ) . '"' : '';

    switch ( $atts['type'] ) {
        case 'image':
            return kingkoil_render_acf_image( $value, $atts['size'], $atts['class'] );

        case 'image_url':
            return kingkoil_get_acf_image_url( $value, $atts['size'] );

        case 'url':
            $link_text = $atts['link_text'] ?: $value;
            return '<a' . $class_attr . ' href="' . esc_url( $value ) . '">' . esc_html( $link_text ) . '</a>';

        case 'html':
        case 'wysiwyg':
            return '<div' . $class_attr . '>' . wp_kses_post( $value ) . '</div>';

        default:
            if ( $class_attr ) {
                return '<span' . $class_attr . '>' . esc_html( $value ) . '</span>';
            }
            return esc_html( $value );
    }
} );

/**
 * Render an ACF image field value as an <img> tag.
 * Handles array, ID, and URL return formats.
 */
function kingkoil_render_acf_image( $value, $size = 'medium', $class = '' ) {
    $class_attr = $class ? [ 'class' => $class ] : [];

    if ( is_array( $value ) ) {
        $id = $value['ID'] ?? 0;
        if ( $id ) {
            return wp_get_attachment_image( $id, $size, false, $class_attr );
        }
        $url = $value['url'] ?? '';
        $alt = $value['alt'] ?? '';
        if ( $url ) {
            $class_html = $class ? ' class="' . esc_attr( $class ) . '"' : '';
            return '<img' . $class_html . ' src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '">';
        }
        return '';
    }

    if ( is_numeric( $value ) ) {
        return wp_get_attachment_image( (int) $value, $size, false, $class_attr );
    }

    // Assume it's a URL string.
    $class_html = $class ? ' class="' . esc_attr( $class ) . '"' : '';
    return '<img' . $class_html . ' src="' . esc_url( $value ) . '" alt="">';
}

/**
 * Get just the URL from an ACF image field value.
 */
function kingkoil_get_acf_image_url( $value, $size = 'medium' ) {
    if ( is_array( $value ) ) {
        // Check for sized URL first, fall back to main URL.
        if ( ! empty( $value['sizes'][ $size ] ) ) {
            return esc_url( $value['sizes'][ $size ] );
        }
        return esc_url( $value['url'] ?? '' );
    }

    if ( is_numeric( $value ) ) {
        $src = wp_get_attachment_image_src( (int) $value, $size );
        return $src ? esc_url( $src[0] ) : '';
    }

    return esc_url( $value );
}
