<?php if ( comments_open() ) { ?><a itemprop="url" class="qodef-e-info-comments-link" href="<?php comments_link(); ?>">
		<?php comments_number( '0 ' . esc_html__( 'Comments', 'swissdelight' ), '1 ' . esc_html__( 'Comment', 'swissdelight' ), '% ' . esc_html__( 'Comments', 'swissdelight' ) ); ?>
	</a><div class="qodef-info-separator-end"></div>
<?php } ?>
