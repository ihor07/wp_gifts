<?php

if ( ! function_exists( 'swissdelight_get_blog_holder_classes' ) ) {
	/**
	 * Function that return classes for the main blog holder
	 *
	 * @return string
	 */
	function swissdelight_get_blog_holder_classes() {
		$classes = array();

		if ( is_single() ) {
			$classes[] = 'qodef--single';
		} else {
			$classes[] = 'qodef--list';
		}

		return implode( ' ', apply_filters( 'swissdelight_filter_blog_holder_classes', $classes ) );
	}
}

if ( ! function_exists( 'swissdelight_get_blog_list_excerpt_length' ) ) {
	/**
	 * Function that return number of characters for excerpt on blog list page
	 *
	 * @return int
	 */
	function swissdelight_get_blog_list_excerpt_length() {
		$length = apply_filters( 'swissdelight_filter_post_excerpt_length', 180 );

		return intval( $length );
	}
}

if ( ! function_exists( 'swissdelight_post_has_read_more' ) ) {
	/**
	 * Function that checks if current post has read more tag set
	 *
	 * @return int position of read more tag text. It will return false if read more tag isn't set
	 */
	function swissdelight_post_has_read_more() {
		global $post;

		return ! empty( $post ) ? strpos( $post->post_content, '<!--more-->' ) : false;
	}
}

if ( ! function_exists( 'swissdelight_custom_post_style' ) ) {
	function swissdelight_custom_post_style( $classes ) {
		if ( swissdelight_is_installed( 'core' ) &&  'yes' === swissdelight_core_get_post_value_through_levels( 'qodef_post_custom_style' )) {

			$classes['custom_post_style'] = 'qodef-post-custom-style';

		}

		return $classes;
	}

	add_filter( 'swissdelight_filter_add_body_classes', 'swissdelight_custom_post_style' );
}
