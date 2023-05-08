<?php if ( has_nav_menu( 'vertical-menu-navigation' ) || has_nav_menu( 'main-navigation' ) ) { ?>
	<nav class="qodef-header-vertical-navigation qodef-vertical-drop-down--below qodef-custom-header-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Vertical Menu', 'swissdelight-core' ); ?>">
		<?php
		// Set main navigation menu as vertical if vertical navigation is not set
		$theme_location = has_nav_menu( 'vertical-menu-navigation' ) ? 'vertical-menu-navigation' : 'main-navigation';

		wp_nav_menu(
			array(
				'theme_location' => $theme_location,
				'container'      => '',
				'link_before'    => '<span class="qodef-menu-item-text">',
				'link_after'     => '</span>',
				'walker'         => new SwissDelightCoreRootMainMenuWalker(),
			)
		);
		?>
	</nav>
<?php } ?>
