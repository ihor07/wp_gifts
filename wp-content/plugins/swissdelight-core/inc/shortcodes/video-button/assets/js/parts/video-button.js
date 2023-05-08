(function ( $ ) {
	'use strict';

	qodefCore.shortcodes.swissdelight_core_video_button = {};

	$( document ).ready(
		function () {
			qodefInitStamp.init();
			qodefInfoFollow.init();
		}
	);

	/**
	 * Inti stamp shortcode on appear
	 */
	var qodefInitStamp = {
		init: function () {
			this.holder = $( '.qodef-stamped, .qodef-e-content-follow' );

			if ( this.holder.length ) {
				this.holder.each(
					function () {
						var $holder         = $( this ),
							appearing_delay = typeof $holder.data( 'appearing-delay' ) !== 'undefined' ? parseInt( $holder.data( 'appearing-delay' ), 10 ) : 0;

						// Initialization
						qodefInitStamp.initStampText( $holder );
						qodefInitStamp.load( $holder, appearing_delay );

						if ( $holder.hasClass( 'qodef--repeating' ) ) {
							setInterval(
								function () {
									qodefInitStamp.reLoad( $holder );
								},
								5500
							);
						}
					}
				);
			}
		},
		initStampText: function ( $holder ) {
			var $stamp = $holder.find( '.qodef-m-text' ),
				count  = typeof $holder.data( 'appearing-delay' ) !== 'undefined' ? parseInt( $stamp.data( 'count' ), 10 ) : 1;

			$stamp.children().each(
				function ( i ) {
					var transform       = -90 + i * 360 / count,
						transitionDelay = i * 60 / count * 10;

					$( this ).css(
						{
							'transform': 'rotate(' + transform + 'deg) translateZ(0)',
							'transition-delay': transitionDelay + 'ms',
						}
					);
				}
			);
		},
		load: function ( $holder, appearing_delay ) {
			if ( $holder.hasClass( 'qodef--nested' ) ) {
				setTimeout(
					function () {
						qodefInitStamp.appear( $holder );
					},
					appearing_delay
				);
			} else {
				$holder.appear(
					function () {
						setTimeout(
							function () {
								qodefInitStamp.appear( $holder );
							},
							appearing_delay
						);
					},
					{ accX: 0, accY: -100 }
				);
			}
		},
		reLoad: function ( $holder ) {
			$holder.removeClass( 'qodef--init' );

			setTimeout(
				function () {
					$holder.removeClass( 'qodef--appear' );

					setTimeout(
						function () {
							qodefInitStamp.appear( $holder );
						},
						500
					);
				},
				600
			);
		},
		appear: function ( $holder ) {
			$holder.addClass( 'qodef--appear' );

			setTimeout(
				function () {
					$holder.addClass( 'qodef--init' );
				},
				300
			);
		}
	};

	var qodefInfoFollow = {
		init: function () {
			var $video = $( '.qodef-hover-animation--follow' );

			if ( $video.length && qodefCore.windowWidth > 1024 ) {
				qodefCore.body.append( '<div class="qodef-e-content-follow"><div class="qodef-m-play-inner"><div class="qodef-m-text"></div><div class="qodef-icon-ionicons ion-md-arrow-dropright"></div></div></div>' );

				var $contentFollow = $( '.qodef-e-content-follow' ),
					$topHolder     = $contentFollow.find( '.qodef-m-text' ),
					$textHolder    = $contentFollow.find( '.qodef-icon-ionicons.ion-md-arrow-dropright' );

				$video.each(
					function () {
						$video.find( '.qodef-m-play' ).each(
							function () {
								var $thisItem = $( this );

								//info element position
								$thisItem.on(
									'mousemove',
									function ( e ) {
										if ( e.clientX + 20 + $contentFollow.width() > qodefCore.windowWidth ) {
											$contentFollow.addClass( 'qodef-right' );
										} else {
											$contentFollow.removeClass( 'qodef-right' );
										}

										$contentFollow.css(
											{
												top: e.clientY - 110,
												left: e.clientX - 110,
												cursor: 'none'
											}
										);
									}
								);

								//show/hide info element
								$thisItem.on(
									'mouseenter',
									function () {
										var $thisItemTopHolder  = $( this ).find( '.qodef-m-text' ),
											$thisItemTextHolder = $( this ).find( '.qodef-icon-ionicons.ion-md-arrow-dropright' );

										if ( $thisItemTopHolder.length ) {
											$topHolder.html( $thisItemTopHolder.html() );
										}

										if ( $thisItemTextHolder.length ) {
											$textHolder.html( $thisItemTextHolder.html() );
										}

										if ( ! $contentFollow.hasClass( 'qodef-is-active' ) ) {
											$contentFollow.addClass( 'qodef-is-active' );
										}
									}
								).on(
									'mouseleave',
									function () {
										if ( $contentFollow.hasClass( 'qodef-is-active' ) ) {
											$contentFollow.removeClass( 'qodef-is-active' );
										}
									}
								);
							}
						);
					}
				);
			}
		}
	};

	qodefCore.shortcodes.swissdelight_core_video_button.qodefInitStamp 	   = qodefInitStamp;
	qodefCore.shortcodes.swissdelight_core_video_button.qodefInfoFollow    = qodefInfoFollow;
	qodefCore.shortcodes.swissdelight_core_video_button.qodefMagnificPopup = qodef.qodefMagnificPopup;

})( jQuery );
