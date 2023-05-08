(function ($) {
	"use strict";
	
	$(document).ready(function () {
		qodefGoogleLogin.init();
	});
	
	$(document).on('swissdelight_membership_social_login_is_triggered', function (e, $modal, $form, social) {
		if (qodefGoogleLogin.isAppIdSet() && social === 'google') {
			qodefGoogleLogin.login($modal, $form, social);
		}
	});
	
	var qodefGoogleLogin = {
		init: function () {
			
			if (qodefGoogleLogin.isAppIdSet()) {
				qodefGoogleLogin.asyncInit(swissdelightMembershipGlobal.googleAppId);
			}
		},
		isAppIdSet: function () {
			return typeof swissdelightMembershipGlobal.googleAppId !== 'undefined' && swissdelightMembershipGlobal.googleAppId !== '';
		},
		asyncInit: function (appID) {
			
			if (appID !== '') {
				gapi.load('auth2', function () {
					window.auth2 = gapi.auth2.init({
						client_id: appID
					});
				});
			}
		},
		login: function ($modal, $form, social) {
			
			window.auth2.signIn().then(qodefGoogleLogin.signIn($modal, $form, social), function (e) {
				console.log(e);
			});
		},
		signIn: function ($modal, $form, social) {
			var signedIn = window.auth2.isSignedIn.get();
	
			if (signedIn) {
				qodefGoogleLogin.getUserData($modal, $form, social);
			} else {
				window.gapi.auth2.getAuthInstance().isSignedIn.listen(function (signedIn) {
					if (signedIn) {
						qodefGoogleLogin.getUserData($modal, $form, social);
					}
				});
			}
		},
		getUserData: function ($modal, $form, social) {
			var currentUser = window.auth2.currentUser.get(),
				profile = currentUser.getBasicProfile(),
				response = {
					id: profile.getId(),
					name: profile.getName(),
					email: profile.getEmail(),
					image: profile.getImageUrl()
				};
			
			if (!$form.hasClass('qodef--loading')) {
				$modal.triggerRequest($form, social, response);
			}
		}
	};
	
})(jQuery);