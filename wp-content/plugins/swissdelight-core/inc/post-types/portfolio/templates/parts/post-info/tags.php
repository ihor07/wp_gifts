<?php
$tags           = wp_get_post_terms( get_the_ID(), 'portfolio-tag' );
$portfolio_tags = swissdelight_core_get_post_value_through_levels( 'qodef_enable_portfolio_tags' );

if ( ! empty( $tags ) && count( $tags ) && 'yes' === $portfolio_tags ) { ?>
	<div class="qodef-e qodef-info--tag">
		<h6 class="qodef-e-title"><?php esc_html_e( 'Tags : ', 'swissdelight-core' ); ?></h6>
		<?php echo get_the_term_list( get_the_ID(), 'portfolio-tag', '', '<span class="qodef-tag-separator"></span>' ); ?>
		<div class="qodef-info-separator-end"></div>
	</div>
<?php } ?>
