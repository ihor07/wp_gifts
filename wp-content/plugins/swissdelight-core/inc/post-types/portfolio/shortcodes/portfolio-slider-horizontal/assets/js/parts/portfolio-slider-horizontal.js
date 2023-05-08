(function ($) {
	
    "use strict";
	
	$(document).ready(function () {
	});
	
	$(window).on('load',function () {
		qodefPortfolioSliderHorizontal.init();
	});
	
	var qodefPortfolioSliderHorizontal = {
		init: function () {
			var $portfolioSliderHorizontal = $('.qodef-portfolio-slider-horizontal');
			
			if ($portfolioSliderHorizontal.length) {

				$portfolioSliderHorizontal.each(function() {
					var thisPortfolioSliderHorizontal = $(this),
                        $swiperContainer = thisPortfolioSliderHorizontal.find('.qodef-swiper-container > .swiper-wrapper'),
						swiperInstance = thisPortfolioSliderHorizontal.find('.qodef-swiper-container'),
                        $items = thisPortfolioSliderHorizontal.find('.qodef-e.swiper-slide'),
                        $headerHeight = $('#qodef-page-header').outerHeight(),
                        $mobileHeaderHeight = $('#qodef-page-mobile-header').outerHeight(),
                        $topBar = $('#qodef-top-area'),
                        $footer = $('#qodef-page-footer'),
                        $sliderBottom = thisPortfolioSliderHorizontal.find('.qodef-m-bottom'),
                        $title = $('.qodef-page-title'),
                        $titleHeight,
                        $footerHeight,
                        $sliderBottomHeight,
                        $height,
                        $topBarHeight;

                    if ( $topBar.length && qodef.windowWidth > 768 ) {
                        $topBarHeight = $topBar.outerHeight();
                    } else {
                        $topBarHeight = 0;
                    }

                    if($footer.length) {
                        $footerHeight = $footer.outerHeight();
                    } else {
                        $footerHeight = 0;
                    }

                    if($sliderBottom.length) {
                        $sliderBottomHeight = $sliderBottom.outerHeight();
                    } else {
                        $sliderBottomHeight = 0;
                    }

                    if($title.length) {
                        $titleHeight = $title.outerHeight();
                    } else {
                        $titleHeight = 0;
                    }

                    if(qodef.windowWidth <= 768) {
                        $height = qodef.windowHeight - $mobileHeaderHeight - $topBarHeight - $titleHeight - $footerHeight - $sliderBottomHeight - 30;
                    }
                    else if(qodef.windowWidth <= 1024) {
						$height = qodef.windowHeight - $mobileHeaderHeight - $topBarHeight - $titleHeight - $footerHeight - $sliderBottomHeight;
					}
                    else {
                        $height = qodef.windowHeight - $headerHeight - $topBarHeight - $titleHeight - $footerHeight - $sliderBottomHeight;
                    }

                    $swiperContainer.height($height);

                    //set mousewheel nagivation
                    var scrollStart = false;
					
					$portfolioSliderHorizontal.on('wheel', function(e) {
						e.preventDefault();
						
						if (!scrollStart) {
							scrollStart = true;
							var delta = e.originalEvent.deltaY;
							
							if (delta > 0) {
								swiperInstance[0].swiper.slideNext();
							} else {
								swiperInstance[0].swiper.slidePrev();
							}
							
							setTimeout(function() {
								scrollStart = false;
							}, 500);
						}
					});
					
				});
			}
		}
	};
 
	qodefCore.shortcodes.swissdelight_core_portfolio_slider_horizontal = {};
	qodefCore.shortcodes.swissdelight_core_portfolio_slider_horizontal.qodefSwiper = qodef.qodefSwiper;

})(jQuery);