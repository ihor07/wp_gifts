<article <?php post_class( $item_classes ); ?>>
	<div class="qodef-e-inner">
		<?php swissdelight_core_template_part( 'blog/shortcodes/blog-list', 'templates/post-info/image', 'background', $params ); ?>
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
				swissdelight_core_template_part( 'blog/shortcodes/blog-list', 'templates/post-info/title', '', $params );
                // Include post excerpt
                swissdelight_core_theme_template_part( 'blog', 'templates/parts/post-info/excerpt', '', $params );
                // Include post read more button
                swissdelight_template_part( 'blog', 'templates/parts/post-info/read-more' );
				?>
			</div>
		</div>
	</div>
</article>
