<div id="qodef-page-comments">
	<?php if ( have_comments() ) {
		$comments_number = get_comments_number();
		?>
		<div id="qodef-page-comments-list" class="qodef-m">
			<?php // translators: %s - number of comments value ?>
			<h4 class="qodef-m-title"><?php esc_html_e( "Comments", 'swissdelight'  ); ?></h4>
			<ul class="qodef-m-comments">
				<?php wp_list_comments( array_unique( array_merge( array( 'callback' => 'swissdelight_get_comments_list_template' ), apply_filters( 'swissdelight_filter_comments_list_template_callback', array() ) ) ) ); ?>
			</ul>

			<?php if ( get_comment_pages_count() > 1 ) { ?>
				<div class="qodef-m-pagination qodef--wp">
					<?php
					the_comments_pagination(
						array(
							'prev_text'          => swissdelight_get_svg_icon( 'pagination-arrow-left' ),
							'next_text'          => swissdelight_get_svg_icon( 'pagination-arrow-right' ),
							'before_page_number' => '',
						)
					);
					?>
				</div>
			<?php } ?>
		</div>
	<?php } ?>
	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) { ?>
		<p class="qodef-page-comments-not-found"><?php esc_html_e( 'Comments are closed.', 'swissdelight' ); ?></p>
	<?php } ?>
	<div id="qodef-page-comments-form">
		<?php comment_form( swissdelight_get_comment_form_args() ); ?>
	</div>
</div>
