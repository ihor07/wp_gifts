(function ( $ ) {
	'use strict';

	$( document ).ready(
	    function () {
            qodefBackToTop.init();
        }
	);

	var qodefBackToTop = {
		init: function () {
			this.holder = $( '#qodef-back-to-top' );

			if ( this.holder.length ) {
				// Scroll To Top
				this.holder.on(
					'click',
					function ( e ) {
						e.preventDefault();
						qodefBackToTop.animateScrollToTop();
					}
				);

				qodefBackToTop.showHideBackToTop();
				qodefBackToTop.changeSkin();
			}
		},
		animateScrollToTop: function () {
			var startPos = qodef.scroll,
				newPos   = qodef.scroll,
				step     = .9,
				animationFrameId;

			var startAnimation = function () {
				if ( newPos === 0 ) {
                    return;
                }

				newPos < 0.0001 ? newPos = 0 : null;

				var ease = qodefBackToTop.easingFunction( (startPos - newPos) / startPos );
				$( 'html, body' ).scrollTop( startPos - (startPos - newPos) * ease );
				newPos = newPos * step;

				animationFrameId = requestAnimationFrame( startAnimation );
			};
			startAnimation();
			$( window ).one(
				'wheel touchstart',
				function () {
					cancelAnimationFrame( animationFrameId );
				}
			);
		},
		easingFunction: function ( n ) {
			return 0 == n ? 0 : Math.pow( 1024, n - 1 );
		},
		showHideBackToTop: function () {
			$( window ).scroll( function () {
				var $thisItem = $( this ),
					b         = $thisItem.scrollTop(),
					c         = $thisItem.height(),
					d;

				if ( b > 0 ) {
					d = b + c / 2;
				} else {
					d = 1;
				}

				if ( d < 1e3 ) {
					qodefBackToTop.addClass( 'off' );
				} else {
					qodefBackToTop.addClass( 'on' );
				}
			} );
		},
		addClass: function ( a ) {
			this.holder.removeClass( 'qodef--off qodef--on' );

			if ( a === 'on' ) {
				this.holder.addClass( 'qodef--on' );
			} else {
				this.holder.addClass( 'qodef--off' );
			}
		},
		changeSkin: function () {
			var $btt = $('#qodef-back-to-top'),
				$skinElements = $('.qodef-row-btt-light'),
				skinSet = false,
				skinTrigger = new Array();

			//Control button skin
			var bttSkin = function () {
				if ( $skinElements.length ) {
					$skinElements.each( function ( i ) {
						var $skinElement = $(this);

						if ( qodef.scroll + $btt.position().top >= $skinElement.offset().top && qodef.scroll + $btt.position().top <= $skinElement.offset().top + $skinElement.outerHeight() ) {
							skinTrigger[i] = true;
						} else {
							skinTrigger[i] = false;
						}
					});

					if ( jQuery.inArray(true, skinTrigger) != -1 ) {
						if ( !skinSet ) {
							$btt.addClass('qodef--light');
							skinSet = true;
						}
					} else {
						if ( skinSet ) {
							$btt.removeClass('qodef--light');
							skinSet = false;
						}
					}
				}
			}

			if ( $btt.length && $skinElements.length ) {
				$(window).scroll( function () {
					bttSkin();
				});
			}
		}
	};

})( jQuery );
