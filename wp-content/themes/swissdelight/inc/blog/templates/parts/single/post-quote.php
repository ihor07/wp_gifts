<article <?php post_class( 'qodef-blog-item qodef-e' ); ?>>
	<div class="qodef-e-inner">
		<?php
		// Include post format part
		swissdelight_template_part( 'blog', 'templates/parts/post-format/quote' );
		?>
		<div class="qodef-e-content">
			<div class="qodef-e-info qodef-e-top-holder">
				<?php
				// Include post date info
				swissdelight_template_part( 'blog', 'templates/parts/post-info/date' );

				// Include post category info
				swissdelight_template_part( 'blog', 'templates/parts/post-info/categories' );
				?>
			</div>
			<div class="qodef-e-text">
				<?php
				// Include post content
				the_content();

				// Hook to include additional content after blog single content
				do_action( 'swissdelight_action_after_blog_single_content' );
				?>
			</div>
			<div class="qodef-e-info qodef-e-bottom-holder">
				<div class="qodef-e-left qodef-e-info">
					<?php
					// Include post tags info
					swissdelight_template_part( 'blog', 'templates/parts/post-info/tags' );
					?>
				</div>
				<div class="qodef-e-right qodef-e-info">
					<?php
					// Include post social share
					swissdelight_template_part( 'blog', 'templates/parts/post-info/social-share' );
					?>
				</div>
			</div>
		</div>
	</div>
</article>
