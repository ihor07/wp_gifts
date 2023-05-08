<article <?php post_class( $item_classes ); ?>>
	<div class="qodef-e-inner">
		<?php
		// Include post media
		swissdelight_core_template_part( 'blog/shortcodes/blog-list', 'templates/post-info/media', '', $params );
		?>
		<div class="qodef-e-content">
			<div class="qodef-e-top-holder">
				<div class="qodef-e-info">
					<?php
					// Include post date info
					swissdelight_core_theme_template_part( 'blog', 'templates/parts/post-info/date' );

					// Include post category info
					swissdelight_core_theme_template_part( 'blog', 'templates/parts/post-info/categories' );
					?>
				</div>
			</div>
			<div class="qodef-e-text">
				<?php
				// Include post title
				swissdelight_core_template_part( 'blog/shortcodes/blog-list', 'templates/post-info/title', '', $params );

				// Include post excerpt
				swissdelight_core_theme_template_part( 'blog', 'templates/parts/post-info/excerpt', '', $params );

				// Hook to include additional content after blog single content
				do_action( 'swissdelight_action_after_blog_single_content' );
				?>
			</div>
			<div class="qodef-e-bottom-holder">
				<?php
				// Include post comments social share
				swissdelight_core_template_part( 'blog/shortcodes/blog-list', 'templates/post-info/social-share' );
				?>
			</div>
		</div>
	</div>
</article>
