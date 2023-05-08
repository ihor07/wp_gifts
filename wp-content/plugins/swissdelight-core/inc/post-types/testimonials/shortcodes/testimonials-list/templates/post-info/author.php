<?php
$author     = get_post_meta( get_the_ID(), 'qodef_testimonials_author', true );
$author_job = get_post_meta( get_the_ID(), 'qodef_testimonials_author_job', true );
$author_avatar = get_post_meta( get_the_ID(), 'qodef_testimonials_author_avatar', true );

if ( ! empty( $author ) ) { ?>
	<div class="qodef-e-author">
		<span class="qodef-e-author-avatar"><?php echo wp_get_attachment_image( $author_avatar, 'full' ); ?></span>
		<div class="qodef-e-author-info">
			<h5 class="qodef-e-author-name"><?php echo esc_html( $author ); ?></h5>
			<span class="qodef-e-author-job"><?php echo esc_html( $author_job ); ?></span>
		</div>
	</div>
<?php } ?>
