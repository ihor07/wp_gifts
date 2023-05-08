<div <?php qode_framework_class_attribute( $holder_classes ); ?>>
	<div class="qodef-m-items">
		<?php
		foreach ( $items as $item ) {
			swissdelight_core_template_part( 'shortcodes/nutritional-values-list', 'variations/list/templates/parts/item', '', array_merge( $params, array( 'item' => $item ) ) );
		}
		?>
	</div>
</div>
