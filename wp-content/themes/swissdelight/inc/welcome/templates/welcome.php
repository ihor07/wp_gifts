<div class="wrap about-wrap qodef-welcome-page">
	<div class="qodef-welcome-page-heading">
		<div class="qodef-welcome-page-logo">
			<img src="<?php echo esc_url( SWISSDELIGHT_INC_ROOT . '/welcome/assets/img/logo.png' ); ?>" alt="<?php esc_attr_e( 'Qode Logo', 'swissdelight' ); ?>"/>
		</div>
		<h1 class="qodef-welcome-page-title">
			<?php
			// translators: %s - added theme name text
			echo sprintf( esc_html__( 'Welcome to %s', 'swissdelight' ), esc_attr( $theme_name ) );
			?>
			<small><?php echo esc_html( $theme_version ); ?></small>
		</h1>
	</div>
	<div class="qodef-welcome-page-text">
		<?php
		echo sprintf(
			// translators: %1$s - added theme name text, %2$s - added theme description text, %3$s - added theme name text
			esc_html__( 'Thank you for installing %1$s - %2$s! Everything in %3$s is streamlined to make your website building experience as simple and fun as possible. We hope you love using it to make a spectacular website.', 'swissdelight' ),
			esc_attr( $theme_name ),
			esc_attr( $theme_description ),
			esc_attr( $theme_name )
		);
		?>
	</div>
	<div class="qodef-welcome-page-content">
		<div class="qodef-welcome-page-screenshot">
			<img src="<?php echo esc_url( $theme_screenshot ); ?>" alt="<?php esc_attr_e( 'Theme Screenshot', 'swissdelight' ); ?>"/>
		</div>
		<div class="qodef-welcome-page-links-holder">
			<div class="qodef-welcome-page-install-core">
				<p><?php esc_html_e( 'Please install and activate required plugins in order to gain access to all the theme functionalities and features.', 'swissdelight' ); ?></p>
				<a class="qodef-welcome-page-install-button" href="<?php echo add_query_arg( array( 'page' => 'install-required-plugins&plugin_status=install' ), esc_url( admin_url( 'themes.php' ) ) ); ?>">
					<?php esc_html_e( 'Install Required Plugins', 'swissdelight' ); ?>
				</a>
			</div>

			<h3><?php esc_html_e( 'Useful Links:', 'swissdelight' ); ?></h3>
			<ul class="qodef-welcome-page-links">
				<li>
					<a href="https://helpcenter.qodeinteractive.com" target="_blank"><?php esc_html_e( 'Help Center', 'swissdelight' ); ?></a>
				</li>
				<li>
					<a href="https://swissdelight.qodeinteractive.com/documentation/" target="_blank"><?php esc_html_e( 'Theme Documentation', 'swissdelight' ); ?></a>
				</li>
				<li>
					<a href="https://qodeinteractive.com/" target="_blank"><?php esc_html_e( 'All Our Themes', 'swissdelight' ); ?></a>
				</li>
				<li>
					<a href="<?php echo add_query_arg( array( 'page' => 'install-required-plugins&plugin_status=install' ), esc_url( admin_url( 'themes.php' ) ) ); ?>"><?php esc_html_e( 'Install Required Plugins', 'swissdelight' ); ?></a>
				</li>
			</ul>
		</div>
	</div>
</div>
