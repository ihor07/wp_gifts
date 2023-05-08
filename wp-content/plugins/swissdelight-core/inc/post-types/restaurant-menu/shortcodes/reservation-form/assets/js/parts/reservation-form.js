(function ( $ ) {
	'use strict';

	qodefCore.shortcodes.swissdelight_core_reservation_form = {};

	$( document ).ready(
		function () {
			qodefReservationForm.init();
		}
	);

	$( document ).on(
		'qodefAjaxPageLoad',
		function () {
			qodefReservationForm.init();
		}
	);

	var qodefReservationForm = {
		init: function () {
			this.holder = $( '.qodef-reservation-form' );

			if ( this.holder.length ) {
				this.holder.each(
					function () {
						var $thisHolder = $( this );

						qodefReservationForm.initDatePicker( $thisHolder );
						qodefReservationForm.initSelect2( $thisHolder );
						qodefReservationForm.trigger( $thisHolder );
					}
				);
			}
		},
		initDatePicker: function ( $holder ) {
			var $datepicker = $holder.find( '.qodef-m-date' );

			if ( $datepicker.length ) {
				$datepicker.each(
					function () {
						$( this ).datepicker(
							{
								prevText: '<span class="arrow_carrot-left"></span>',
								nextText: '<span class="arrow_carrot-right"></span>',
								dateFormat: 'M d, yy',
							}
						);
					}
				);
			}
		},
		initSelect2: function ( $holder ) {
			var $select = $holder.find( '.qodef-m-field select' );

			if ( $select.length && typeof $select.select2 === 'function' ) {
				$select.select2(
					{
						minimumResultsForSearch: Infinity,
					}
				);
			}
		},
		trigger: function ( $holder ) {
			var $form = $holder.find( 'form' );

			$form.on(
				'submit',
				function ( e ) {
					e.preventDefault();

					var inputValues = $form.serializeArray(),
						datetime    = '';

					$.each(
						inputValues,
						function () {
							var $input    = $( this )[0],
								inputName = $input.name;

							if ( inputName === 'date' || inputName === 'time' ) {
								datetime += ' ' + $input.value;
							}
						}
					);

					if ( datetime.length ) {
						var date          = new Date( datetime ),
							formattedDate = date.getFullYear() + '-' + (parseInt( date.getMonth(), 10 ) < 10 ? '0' : '') + (parseInt( date.getMonth(), 10 ) + 1) + '-' + (parseInt( date.getDate(), 10 ) < 10 ? '0' : '') + date.getDate() + 'T' + (parseInt( date.getHours(), 10 ) + 1) + ':' + date.getMinutes() + (parseInt( date.getMinutes(), 10 ) == 30 ? '' : '0');

						$form.find( '[name="datetime"]' ).val( formattedDate );
					}

					window.open(
						$form.attr( 'action' ) + '?' + $form.serialize(),
						'_blank'
					);
				}
			);
		}
	};

	qodefCore.shortcodes.swissdelight_core_reservation_form.qodefReservationForm = qodefReservationForm;

})( jQuery );
