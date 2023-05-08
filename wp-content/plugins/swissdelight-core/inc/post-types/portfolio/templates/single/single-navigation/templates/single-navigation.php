<?php
$is_enabled = swissdelight_core_get_post_value_through_levels( 'qodef_portfolio_enable_navigation' );

if ( 'yes' === $is_enabled ) {
	$through_same_category = 'yes' === swissdelight_core_get_post_value_through_levels( 'qodef_portfolio_navigation_through_same_category' );
	?>
	<div id="qodef-single-portfolio-navigation" class="qodef-m">
		<div class="qodef-m-inner">
			<?php
			$navigation_icon_params = array(
				'icon_attributes' => array(
					'class' => 'qodef-m-nav-icon',
				),
			);
			$post_navigation        = array(
				'prev'      => array(
					'label' => '<span class="qodef-m-nav-label">' . esc_html__( 'Previous', 'swissdelight-core' ) . '</span>',
					'icon'  => qode_framework_icons()->render_icon( 'arrow_carrot-left', 'elegant-icons', $navigation_icon_params ),
				),
				'back-link' => array(),
				'next'      => array(
					'label' => '<span class="qodef-m-nav-label">' . esc_html__( 'Next', 'swissdelight-core' ) . '</span>',
					'icon'  => qode_framework_icons()->render_icon( 'arrow_carrot-right', 'elegant-icons', $navigation_icon_params ),
				),
			);

			if ( $through_same_category ) {
				if ( '' !== get_adjacent_post( true, '', true, 'portfolio-category' ) ) {
					$post_navigation['prev']['post'] = get_adjacent_post( true, '', true, 'portfolio-category' );
				}
				if ( '' !== get_adjacent_post( true, '', false, 'portfolio-category' ) ) {
					$post_navigation['next']['post'] = get_adjacent_post( true, '', false, 'portfolio-category' );
				}
			} else {
				if ( '' !== get_adjacent_post( false, '', true ) ) {
					$post_navigation['prev']['post'] = get_adjacent_post( false, '', true );
				}
				if ( '' !== get_adjacent_post( false, '', false ) ) {
					$post_navigation['next']['post'] = get_adjacent_post( false, '', false );
				}
			}

			$back_to_link = get_post_meta( get_the_ID(), 'qodef_portfolio_single_back_to_link', true );
			if ( '' !== $back_to_link ) {
				$post_navigation['back-link'] = array(
					'post'    => true,
					'post_id' => $back_to_link,
					'icon'    => qode_framework_icons()->render_icon( 'icon_menu', 'elegant-icons', $navigation_icon_params ),
				);
			}

			foreach ( $post_navigation as $key => $value ) {
				if ( isset( $post_navigation[ $key ]['post'] ) ) {
					$current_post = $value['post'];
					$post_id      = $current_post->ID;

					$categories = wp_get_post_terms( get_the_ID(), 'portfolio-category' );
					$separator  = ', ';
					$output     = '';

					?>
					<a itemprop="url" class="qodef-m-nav qodef--<?php echo esc_attr( $key ); ?>"
					href="<?php echo get_permalink( $post_id ); ?>">
						<span class="qodef-m-nav-image">
							<?php echo get_the_post_thumbnail( $post_id, 'thumbnail' ); ?>
						</span>
						<span class="qodef-m-nav-info">
							<span class="qodef-m-top">
								<span class="qodef-m-nav-title">
									<?php echo get_the_title( $post_id ); ?>
								</span>
							</span>
							<span class="qodef-m-nav-cat">
									<?php
									if ( $categories ) {
										foreach ( $categories as $category ) {
											$output .= $category->name . $separator;
										}
										echo trim( $output, $separator );
									}
									?>
								</span>
						</span>

					</a>
					<?php
				}
			}
			?>
		</div>
	</div>
<?php } ?>
