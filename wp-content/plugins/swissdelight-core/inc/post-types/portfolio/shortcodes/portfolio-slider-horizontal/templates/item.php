<article class="qodef-e qodef-grid-item swiper-slide" data-index="<?php echo esc_attr($current_id); ?>">
	<div class="qodef-e-inner">
		<div class="qodef-e-image">
			<?php $params['image_dimension'] = $this_shortcode->get_list_item_image_dimension( $params );
			$params['item_classes']    = $this_shortcode->get_item_classes( $params );
			$params['current_id']    = get_the_ID();
			
			$portfolio_list_image = get_post_meta( get_the_ID(), 'qodef_portfolio_list_image', true );
			$has_image            = ! empty ( $portfolio_list_image ) || has_post_thumbnail();
			
			if ( $has_image ) {
			?>
			<div class="qodef-e-media-image">
				<a itemprop="url" href="<?php the_permalink(); ?>">
					<?php echo swissdelight_core_get_list_shortcode_item_image( 'full', $portfolio_list_image); ?>
				</a>
			</div>
			<?php } ?>
			<div class="qodef-e-content">
				<h5 itemprop="name" class="qodef-e-title entry-title">
					<a itemprop="url" class="qodef-e-title-link" href="<?php the_permalink(); ?>">
						<?php the_title(); ?>
					</a>
				</h5>
				<?php swissdelight_core_list_sc_template_part( 'post-types/portfolio/shortcodes/portfolio-list', 'post-info/categories', '', $params ); ?>
			</div>
		</div>
	</div>
</article>