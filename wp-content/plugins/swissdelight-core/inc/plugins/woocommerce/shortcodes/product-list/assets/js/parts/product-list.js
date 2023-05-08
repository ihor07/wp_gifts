(function ( $ ) {
	'use strict';

	var shortcode = 'swissdelight_core_product_list';
	
	$( document ).on(
		'swissdelight_trigger_get_new_posts',
		function ( e, $holder, response, nextPage ) {
			if ( $holder.hasClass( 'qodef-woo-product-list' ) ) {
				qodefProductListFilter.init( $holder, response, nextPage );
			}
		}
	);

	$( window ).on(
		'load',
		function () {
			qodefProductListFilter.init();
		}
	);

	var qodefProductListFilter = {
		init: function () {
			var $productList = $( '.qodef-woo-product-list' );

			if ( $productList.length ) {
				$productList.each(
					function () {
						var $thisProductList = $( this ),
							$fields			 = [];

						$fields.$orderbyFields 		 = $productList.find( '.qodef-ordering-filter-link' );
						$fields.orderbyFieldsExists  = $fields.$orderbyFields.length;
						$fields.$categoryFields 	 = $productList.find( '.qodef-category-filter-link' );
						$fields.categoryFieldsExists = $fields.$categoryFields.length;

						qodefProductListFilter.initFilter( $thisProductList, $fields );
					}
				);
			}
		},
		initFilter: function( $list, $fields ) {
			var links = $list.find( '.qodef-category-filter-link, .qodef-ordering-filter-link' );

			links.on(
				'click',
				function(e) {
					e.preventDefault();
					e.stopPropagation();

					var clickedLink = $( this );
					if ( ! clickedLink.hasClass( 'qodef--active' ) ) {

						clickedLink.addClass( 'qodef--active' );
						clickedLink.parent().siblings().find( 'a' ).removeClass( 'qodef--active' );

						var options    = $list.data( 'options' ),
							newOptions = {};

						if ($fields.orderbyFieldsExists) {
							$fields.$orderbyFields.each(
								function () {
									if ( $( this ).hasClass( 'qodef--active' ) ) {
										var orderKey = 'order_by',
											value    = $( this ).data( 'ordering' );

										if (typeof value !== "undefined" && value !== "") {
											newOptions[orderKey] = value;
										} else {
											newOptions[orderKey] = '';
										}
									}
								}
							);
						}

						if ($fields.categoryFieldsExists) {
							$fields.$categoryFields.each(
								function () {
									if ( $( this ).hasClass( 'qodef--active' ) ) {
										var categoryKey = 'category',
											value 		= $( this ).data( 'category' );

										if (typeof value !== "undefined" && value !== "") {
											newOptions[categoryKey] = value;
										} else {
											newOptions[categoryKey] = '';
										}
									}
								}
							);
						}

						var additional = qodefProductListFilter.createAdditionalQuery( newOptions );

						$.each(
							additional,
							function (key, value) {
								options[key] = value;
							}
						);

						$list.data( 'options',options );

						qodef.body.trigger( 'swissdelight_trigger_load_more', [$list, 1] );

					}
				}
			);
		},
		createAdditionalQuery: function( newOptions ){
			var addQuery 		= {},
				taxQueryOptions = {},
				categories 		= $( '.qodef-category-filter-link' );

			addQuery.additional_query_args 			 = {};
			addQuery.additional_query_args.tax_query = [];

			if (typeof newOptions === 'object') {
				$.each(
					newOptions,
					function ( key, value ) {

						switch (key) {
							case 'order_by':
								addQuery.orderby = newOptions.order_by;
								break;
							case 'category':
								taxQueryOptions = {
									0: {
										taxonomy: 'product_cat',
										field: typeof value === 'number' ? 'term_id' : 'slug',
										terms: value,
									}
								};
						}
					}
				);

				if ( categories.length && taxQueryOptions[0].terms.length > 0 ) {
					addQuery.additional_query_args = {
						tax_query: taxQueryOptions,
					};
				}
			}

			return addQuery;
		},
	};

	qodefCore.shortcodes[shortcode] = {};
	qodefCore.shortcodes[shortcode].qodefProductListFilter = qodefProductListFilter;

	if ( typeof qodefCore.listShortcodesScripts === 'object' ) {
		$.each(
			qodefCore.listShortcodesScripts,
			function ( key, value ) {
				qodefCore.shortcodes[shortcode][key] = value;
			}
		);
	}

})( jQuery );
