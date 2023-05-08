(function ( $ ) {
	'use strict';

	qodefCore.shortcodes.swissdelight_core_image_gallery = {};

	$( document ).ready(
		function () {
			qodefImageGallery.init();
		}
	);

	var qodefImageGallery = {
		init: function () {
			this.galleries = $( '.qodef-image-gallery' );

			if ( this.galleries.length ) {
				this.galleries.each(
					function () {
						var $thisGallery = $( this );

						if ( $thisGallery.hasClass('qodef-swiper-container') && $thisGallery.hasClass('qodef-autoplay--delayed') ) {
							qodefImageGallery.delaySliderAutoplay( $thisGallery );
						}
					}
				);
			}
		},
		delaySliderAutoplay: function ( $gallery ) {
			var $slider = $gallery[0].swiper;

			if ( $slider.params.autoplay.enabled ) {
				$slider.autoplay.stop();

				$gallery.appear( function () {
					$slider.autoplay.start();
				});
			}
		}
	};

	qodefCore.shortcodes.swissdelight_core_image_gallery.qodefImageGallery  = qodefImageGallery;
	qodefCore.shortcodes.swissdelight_core_image_gallery.qodefSwiper        = qodef.qodefSwiper;
	qodefCore.shortcodes.swissdelight_core_image_gallery.qodefMasonryLayout = qodef.qodefMasonryLayout;

})( jQuery );
