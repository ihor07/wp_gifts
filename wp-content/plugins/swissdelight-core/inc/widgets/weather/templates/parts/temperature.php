<div class="qodef-m-temperature">
	<?php
	echo sprintf(
		'%s%s%s',
		esc_html( $today_temp ),
		'<sup>°</sup>',
		esc_html( $temp_unit )
	);
	?>
</div>
