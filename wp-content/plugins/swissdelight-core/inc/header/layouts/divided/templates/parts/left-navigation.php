<?php if ( has_nav_menu( 'divided-menu-left-navigation' ) ) : ?>
	<nav class="qodef-header-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Divided Left Menu', 'swissdelight-core' ); ?>">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'divided-menu-left-navigation',
				'container'      => '',
				'link_before'    => '<span class="qodef-menu-item-text">',
				'link_after'     => '</span>',
				'walker'         => new SwissDelightCoreRootMainMenuWalker(),
			)
		);
		?>
	</nav>
<?php endif; ?>
