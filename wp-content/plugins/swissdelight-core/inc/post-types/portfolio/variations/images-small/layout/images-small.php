<?php
// Hook to include additional content before portfolio single item
do_action( 'swissdelight_core_action_before_portfolio_single_item' );
?>
	<article <?php post_class( 'qodef-portfolio-single-item qodef-variations--small qodef-e' ); ?>>
		<div class="qodef-e-inner">
			<div class="qodef-e-content qodef-grid qodef-layout--template <?php echo swissdelight_core_get_grid_gutter_classes(); ?>">
				<div class="qodef-grid-inner clear">
					<div class="qodef-grid-item qodef-col--7-custom">
						<div class="qodef-media">
							<?php swissdelight_core_template_part( 'post-types/portfolio', 'templates/parts/post-info/media' ); ?>
						</div>
					</div>
					<div class="qodef-grid-item qodef-col--5-custom">
						<?php swissdelight_core_template_part( 'post-types/portfolio', 'templates/parts/post-info/title' ); ?>
						<?php swissdelight_core_template_part( 'post-types/portfolio', 'templates/parts/post-info/content' ); ?>
						<div class="qodef-portfolio-info">
							<?php swissdelight_core_template_part( 'post-types/portfolio', 'templates/parts/post-info/custom-fields' ); ?>
							<?php swissdelight_core_template_part( 'post-types/portfolio', 'templates/parts/post-info/categories' ); ?>
							<?php swissdelight_core_template_part( 'post-types/portfolio', 'templates/parts/post-info/tags' ); ?>
							<?php swissdelight_core_template_part( 'post-types/portfolio', 'templates/parts/post-info/date' ); ?>
							<?php swissdelight_core_template_part( 'post-types/portfolio', 'templates/parts/post-info/social-share' ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</article>
<?php
// Hook to include additional content after portfolio single item
do_action( 'swissdelight_core_action_after_portfolio_single_item' );
?>
