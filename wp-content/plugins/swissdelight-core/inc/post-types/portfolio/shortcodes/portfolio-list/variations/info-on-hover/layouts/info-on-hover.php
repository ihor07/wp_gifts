<?php
	$bg_letter = get_post_meta( get_the_ID(), 'qodef_portfolio_single_bg_hover', true );
?>

<article <?php post_class( $item_classes ); ?>>
	<div class="qodef-e-inner" <?php qode_framework_inline_style( $this_shortcode->get_list_item_style( $params ) ); ?>>
		<div class="qodef-e-image">
			<?php swissdelight_core_list_sc_template_part( 'post-types/portfolio/shortcodes/portfolio-list', 'post-info/image', '', $params ); ?>
		</div>
		<div class="qodef-e-content">
			<div class="qodef-e-content-inner">
				<?php if(!empty($bg_letter)){ ?>
					<span class="qodef-m-bg-letter" data-bg="<?php esc_html_e($bg_letter);?>"></span>
				<?php } ?>
				<a itemprop="url" href="<?php the_permalink(); ?>"></a>
				<?php swissdelight_core_list_sc_template_part( 'post-types/portfolio/shortcodes/portfolio-list', 'post-info/title', '', $params ); ?>
				<?php swissdelight_core_list_sc_template_part( 'post-types/portfolio/shortcodes/portfolio-list', 'post-info/categories', '', $params ); ?>
			</div>
		</div>
	</div>
</article>
