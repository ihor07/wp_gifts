<?php

if ( is_user_logged_in() ) {
	swissdelight_membership_template_part( 'widgets/login-opener', 'templates/logged-in-content' );
} else {
	swissdelight_membership_template_part( 'widgets/login-opener', 'templates/logged-out-content' );
}