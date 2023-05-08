(function ( $ ) {
	'use strict';

	var shortcode = 'swissdelight_core_image_with_text';

	qodefCore.shortcodes[shortcode] = {};

	if (typeof qodefCore.listShortcodesScripts === 'object') {
		$.each(qodefCore.listShortcodesScripts, function (key, value) {
			qodefCore.shortcodes[shortcode][key] = value;
		});
	}

	$(document).on( 'ready', function() {
		qodefImageWithText.init();
	});

	var qodefImageWithText = {
		init: function () {
			this.holder = $('.qodef-image-with-text');

			if ( this.holder.length ) {
				this.holder.each( function () {
					var $thisHolder = $( this );

					qodefImageWithText.linkHover( $thisHolder );
				})
			}
		},
		linkHover: function ( $holder ) {
			var $imageLink = $holder.find('.qodef-m-image a'),
				$titleLink = $holder.find('.qodef-m-title a');

			$imageLink.on('mouseenter', function() {
				$holder.addClass('qodef--active');
			});

			$imageLink.on('mouseleave', function() {
				$holder.removeClass('qodef--active');
			});

			$titleLink.on('mouseenter', function() {
				$holder.addClass('qodef--active');
			});

			$titleLink.on('mouseleave', function() {
				$holder.removeClass('qodef--active');
			});
		}
	}

	qodefCore.shortcodes[shortcode].qodefImageWithText = qodefImageWithText;
	qodefCore.shortcodes[shortcode].qodefMagnificPopup = qodef.qodefMagnificPopup;

})( jQuery );
