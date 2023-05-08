<?php
$about = get_post_meta( get_the_ID(), 'qodef_team_about', true );

if ( ! empty( $about ) ) { ?>
	<p class="qodef-e-biography"><?php echo wp_kses_post( $about ); ?></p>
<?php } ?>
