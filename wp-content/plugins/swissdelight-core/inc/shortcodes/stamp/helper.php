<?php

if ( ! function_exists( 'swissdelight_str_split_unicode' ) ) {
	/**
	 * Function that return characters array of text
	 *
	 * @param string $str
	 *
	 * @return array
	 */
	function swissdelight_str_split_unicode( $str ) {
		return preg_split( '~~u', html_entity_decode( $str ), - 1, PREG_SPLIT_NO_EMPTY );
	}
}

if ( ! function_exists( 'swissdelight_get_split_text' ) ) {
	/**
	 * Function that return modified text value with html tags
	 *
	 * @param string $text
	 *
	 * @return string that contains html content
	 */
	function swissdelight_get_split_text( $text ) {
		if ( ! empty( $text ) ) {
			$split_text = swissdelight_str_split_unicode( $text );

			foreach ( $split_text as $key => $value ) {
				$split_text[ $key ] = '<span class="qodef-e-character">' . $value . '</span>';
			}

			return implode( ' ', $split_text );
		}

		return $text;
	}
}
