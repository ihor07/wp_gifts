<?php
$is_enabled = swissdelight_core_get_post_value_through_levels( 'qodef_blog_single_enable_single_post_navigation' );

if ( 'yes' === $is_enabled ) {
	$through_same_category = 'yes' === swissdelight_core_get_post_value_through_levels( 'qodef_blog_single_post_navigation_through_same_category' );
	?>
	<div id="qodef-single-post-navigation" class="qodef-m">
		<div class="qodef-m-inner">
			<?php
			$post_navigation = array(
				'prev' => array(
					'label' => '<span class="qodef-m-nav-label">' . esc_html__( 'Prev post', 'swissdelight-core' ) . '</span>',
				),
				'next' => array(
					'label' => '<span class="qodef-m-nav-label">' . esc_html__( 'Next post', 'swissdelight-core' ) . '</span>',
				),
			);

			if ( $through_same_category ) {
				if ( '' !== get_previous_post( true ) ) {
					$post_navigation['prev']['post'] = get_previous_post( true );
				}
				if ( '' !== get_next_post( true ) ) {
					$post_navigation['next']['post'] = get_next_post( true );
				}
			} else {
				if ( '' !== get_previous_post() ) {
					$post_navigation['prev']['post'] = get_previous_post();
				}
				if ( '' !== get_next_post() ) {
					$post_navigation['next']['post'] = get_next_post();
				}
			}

			foreach ( $post_navigation as $key => $value ) {
				if ( isset( $post_navigation[ $key ]['post'] ) ) {
					$current_post = $value['post'];
					$post_id      = $current_post->ID;
					$date_format  = get_option( 'date_format' );

					$categories = get_the_category($post_id);
					$separator = ', ';
					$output = '';

					?>
					<a itemprop="url" class="qodef-m-nav qodef--<?php echo esc_attr( $key ); ?>"
					   href="<?php echo get_permalink( $post_id ); ?>">
						<span class="qodef-m-nav-image">
							<?php echo get_the_post_thumbnail( $post_id, 'thumbnail' ); ?>
						</span>
						<span class="qodef-m-nav-info">
							<span class="qodef-m-top">
								<span class="qodef-m-nav-date">
									<?php echo get_the_date( $date_format, $post_id ); ?>
								</span>
								<span class="qodef-m-nav-cat">
									<?php
									if($categories) {
										foreach ( $categories as $category ) {
											$output .= $category->name . $separator;
										}
										echo trim( $output, $separator );
									}
									?>
								</span>
							</span>
							<span class="qodef-m-nav-title">
								<?php echo get_the_title( $post_id ) ?>
							</span>
						</span>

					</a>
				<?php }
			}
			?>
		</div>
	</div>
<?php } ?>
