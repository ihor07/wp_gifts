<?php if ( 'yes' === $product_list_enable_filter_category ) { ?>
	<div class="qodef-category-filter">
		<div class="qodef-category-title-holder">
			<span class="qodef-category-title"><?php esc_html_e( 'Categories', 'swissdelight-core' ); ?></span>
			<i class="icon-arrows-slim-right"></i>
		</div>
		<div class="qodef-category-filter-list">
			<ul class="qodef-category-list">
				<?php echo swissdelight_core_get_product_list_category_filter( $params ); ?>
			</ul>
		</div>
	</div>
<?php } ?>
