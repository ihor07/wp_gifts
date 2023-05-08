<?php if ( 'yes' === $product_list_enable_filter_order_by ) { ?>
	<div class="qodef-ordering-filter">
		<div class="qodef-filter-title-holder">
			<span class="qodef-filter-title"><?php esc_html_e( 'Filter', 'swissdelight-core' ); ?></span>
            <?php swissdelight_render_svg_icon( 'button-arrow', 'qodef-m-svg-icon' ); ?>
		</div>
		<div class="qodef-filter-ordering">
			<div class="qodef-filter-list">
				<ul class="qodef-sorting-filter">
					<li>
						<a class="qodef-ordering-filter-link" data-ordering="<?php echo esc_html( $orderby ); ?>" href="#"><?php echo esc_html__( 'Default', 'swissdelight-core' ); ?></a>
					</li>
					<?php echo swissdelight_core_get_product_list_sorting_filter( $params ); ?>
				</ul>
			</div>
		</div>
	</div>
<?php } ?>
