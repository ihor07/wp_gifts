(function ($) {
	"use strict";
	
	$(document).ready(function () {
		qodefLoginOpener.init();
	});
	
	var qodefLoginOpener = {
		init: function () {
			this.holder = $('.qodef-login-opener-widget');
			
			if (this.holder.length) {
				this.holder.each(function () {
					qodefLoginOpener.triggerClick($(this));
				});
			}
		},
		triggerClick: function ($holder) {
			var $opener = $holder.find('.qodef-login-opener');
			
			$opener.on('click', function (e) {
				e.preventDefault();
				
				$(document.body).trigger('swissdelight_membership_trigger_login_modal');
			});
		}
	};
	
})(jQuery);