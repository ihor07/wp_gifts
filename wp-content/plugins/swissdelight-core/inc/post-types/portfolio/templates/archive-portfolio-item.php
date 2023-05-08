<?php

get_header();

$params            = array();
$params['content'] = 'shortcode';
// Include cpt content template
swissdelight_core_template_part( 'post-types/portfolio', 'templates/content', '', $params );

get_footer();
