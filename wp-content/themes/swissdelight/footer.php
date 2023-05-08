			</div><!-- close #qodef-page-inner div from header.php -->
		</div><!-- close #qodef-page-outer div from header.php -->
		<?php
		// Hook to include page footer template
		do_action( 'swissdelight_action_page_footer_template' );

		// Hook to include additional content before wrapper close tag
		do_action( 'swissdelight_action_before_wrapper_close_tag' );
		?>
	</div><!-- close #qodef-page-wrapper div from header.php -->
	<?php
	// Hook to include additional content before body tag closed
	do_action( 'swissdelight_action_before_body_tag_close' );
	?>
<?php wp_footer(); ?>
<script>
jQuery(document).on('change', '[data-component~="wcpw-product-quantity-input"]', function () {
    var $product = jQuery(this).closest('[data-component="wcpw-product"]');
    var $wcpw = $product.closest('[data-component~="wcpw"]');

    if ($product.data('submitTimeout')) {
        clearTimeout($product.data('submitTimeout'));
    }

    $product.data('submitTimeout', setTimeout(function () {
        $wcpw.data('wcpw').addCartProduct(
            {productToAddKey: $product.data('step-id') + '-' + $product.data('id')},
            {behavior: 'submit'}
        );
    }, 1000));
});
</script>
</body>
</html>
