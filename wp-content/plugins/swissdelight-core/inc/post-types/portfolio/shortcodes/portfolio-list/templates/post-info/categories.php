<?php
$categories = wp_get_post_terms( get_the_ID(), 'portfolio-category' );

if ( ! empty( $categories ) ) { ?>
	<div class="qodef-e qodef-info--category">
		<?php echo get_the_term_list( get_the_ID(), 'portfolio-category', '', '<span class="qodef-category-separator"></span>' ); ?>
		<div class="qodef-info-separator-end"></div>
	</div>
<?php } ?>
