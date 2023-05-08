<?php

if ( ! function_exists( 'swissdelight_core_product_list_filter_query' ) ) {
	/**
	 * Function to adjust query for listing list parameters
	 */
	function swissdelight_core_product_list_filter_query( $args, $params ) {

		switch ( $params['orderby'] ) {

			case 'price-range-high':
				$args['meta_query'] = array(
					array(
						'key'     => '_price',
					),
				);

				$args['order']   = 'DESC';
				$args['orderby'] = 'meta_value_num';
				break;

			case 'price-range-low':
				$args['meta_query'] = array(
					array(
						'key'     => '_price',
					),
				);

				$args['order']   = 'ASC';
				$args['orderby'] = 'meta_value_num';
				break;
		}

		return $args;
	}

	add_filter('swissdelight_filter_query_params', 'swissdelight_core_product_list_filter_query', 10, 2);
}

if ( ! function_exists( 'swissdelight_core_get_product_list_query_order_by_array' ) ) {
	function swissdelight_core_get_product_list_query_order_by_array() {
		$include_order_by = array(
			'price-range-high'	=> esc_html__( 'Price Range from High to Low', 'swissdelight-core' ),
			'price-range-low'	=> esc_html__( 'Price Range From Low To High', 'swissdelight-core' )
		);

		return swissdelight_core_get_select_type_options_pool( 'order_by', false, array(), $include_order_by );
	}
}


if ( ! function_exists( 'swissdelight_core_get_product_list_sorting_filter' ) ) {
	function swissdelight_core_get_product_list_sorting_filter() {
		$sorting_list_html = '';

		$include_order_by = swissdelight_core_get_product_list_query_order_by_array();

		foreach ( $include_order_by as $key => $value ) {
			$sorting_list_html .= '<li><a class="qodef-ordering-filter-link" data-ordering="' . $key . '" href="#">' . $value . '</a></li>';
		}

		return $sorting_list_html;
	}
}

if ( ! function_exists( 'swissdelight_core_get_product_list_category_filter' ) ) {
	function swissdelight_core_get_product_list_category_filter( $params ) {
		$taxonomy_html = '';

		$taxonomy     = 'product_cat';
		$orderby      = 'name';
		$show_count   = 0;      // 1 for yes, 0 for no
		$pad_counts   = 0;      // 1 for yes, 0 for no
		$hierarchical = 1;      // 1 for yes, 0 for no
		$title        = '';
		$empty        = 0;
		$parent       = 0;

		$args = array(
			'taxonomy'     => $taxonomy,
			'orderby'      => $orderby,
			'show_count'   => $show_count,
			'pad_counts'   => $pad_counts,
			'hierarchical' => $hierarchical,
			'title_li'     => $title,
			'hide_empty'   => $empty,
			'parent'       => $parent
		);

		if ( 'tax' === $params['additional_params'] ) {
			$args['taxonomy'] = $params['tax'];
		}


		$all_categories_string = '';

		if ( '' === $params['tax_slug'] ) {
			if ( '' === $params['filter_tax__in'] ) {
				$all_categories = get_categories( $args );
			} else {
				$all_categories = array();
				$categories     = explode( ',', $params['filter_tax__in'] );
				foreach ( $categories as $cat ) {
					$all_categories[] = get_term_by( 'id', $cat, 'product_cat' );
				}
			}
		} else {
			$all_categories_string = $params['tax_slug'];
			$all_categories = array();
			$categories     = explode( ',', $params['tax_slug'] );
			foreach ( $categories as $cat ) {
				$all_categories[] = get_term_by( 'slug', $cat, 'product_cat' );
			}
		}



		$taxonomy_html .= '<li><a class="qodef-category-filter-link qodef--active" data-category="' . $all_categories_string . '" href="#">' . esc_html__( 'All', 'swissdelight-core' ) . '</a></li>';

		foreach ( $all_categories as $cat ) {
			if ( '' !== $cat ) {

				if ( '' === $params['tax_slug'] ) {
					$taxonomy_html .= '<li><a class="qodef-category-filter-link" data-category="' . $cat->slug . '" href="' . get_term_link( $cat->slug, 'product_cat' ) . '">' . $cat->name . '</a></li>';
				}

				$termchildren = get_term_children( $cat->term_id, 'product_cat' );

				if ( ! empty( $termchildren ) ) {
					foreach ( $termchildren as $child ) {
						$child_cat = get_term_by( 'id', $child, 'product_cat' );

						if ( ! is_wp_error( $child_cat ) && ! empty( $child_cat ) ) {
							$taxonomy_html .= '<li><a class="qodef-category-filter-link" data-category="' . $child_cat->slug . '" href="' . get_term_link( $child_cat->slug, 'product_cat' ) . '">' . $child_cat->name . '</a></li>';
						}
					}
				}
			}
		}

		return $taxonomy_html;
	}
}
