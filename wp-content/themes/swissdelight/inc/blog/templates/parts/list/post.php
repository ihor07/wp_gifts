<article <?php post_class( 'qodef-blog-item qodef-e' ); ?>>
	<div class="qodef-e-inner">
		<?php
		// Include post media
		swissdelight_template_part( 'blog', 'templates/parts/post-info/media' );
		?>
		<div class="qodef-e-content">
			<div class="qodef-e-top-holder">
				<div class="qodef-e-info">
					<?php
					// Include post date info
					swissdelight_template_part( 'blog', 'templates/parts/post-info/date' );

					// Include post category info
					swissdelight_template_part( 'blog', 'templates/parts/post-info/categories' );
					?>
				</div>
			</div>
			<div class="qodef-e-text">
				<?php
				// Include post title
				swissdelight_template_part( 'blog', 'templates/parts/post-info/title', '', array( 'title_tag' => 'h2' ) );

				// Include post excerpt
				swissdelight_template_part( 'blog', 'templates/parts/post-info/excerpt' );

				// Hook to include additional content after blog single content
				do_action( 'swissdelight_action_after_blog_single_content' );
				?>
			</div>
			<div class="qodef-e-bottom-holder">
				<?php
				if ( ! swissdelight_is_installed( 'core' ) ) {
					?>
					<div class="qodef-e-bottom-left">
						<?php swissdelight_template_part( 'blog', 'templates/parts/post-info/tags' ); ?>
					</div>
					<?php
				} else {
					// Include post comments info
					swissdelight_template_part( 'blog', 'templates/parts/post-info/social-share' );
				}
				?>
			</div>
		</div>
	</div>
</article>
