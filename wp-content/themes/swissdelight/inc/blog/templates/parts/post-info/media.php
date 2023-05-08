<div class="qodef-e-media">
	<?php
	switch ( get_post_format() ) {
		case 'gallery':
			swissdelight_template_part( 'blog', 'templates/parts/post-format/gallery' );
			break;
		case 'video':
			swissdelight_template_part( 'blog', 'templates/parts/post-format/video' );
			break;
		case 'audio':
			swissdelight_template_part( 'blog', 'templates/parts/post-format/audio' );
			break;
		default:
			swissdelight_template_part( 'blog', 'templates/parts/post-info/image' );
			break;
	}
	?>
</div>
