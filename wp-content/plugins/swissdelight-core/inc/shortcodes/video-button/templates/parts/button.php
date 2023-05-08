<?php if ( ! empty( $video_link ) ) { ?>
	<a itemprop="url" class="qodef-m-play qodef-magnific-popup qodef-popup-video-item" <?php echo qode_framework_get_inline_style( $play_button_styles ); ?> href="<?php echo esc_url( $video_link ); ?>" data-type="iframe">
        <span class="qodef-m-play-inner">
            <span class="qodef-m-text" data-count="<?php echo esc_attr( $text_data['count'] ); ?>"><?php echo qode_framework_wp_kses_html( 'content', $text_data['text'] ); ?></span>
            <?php echo qode_framework_icons()->render_icon( 'ion-md-arrow-dropright', 'ionicons' ); ?>
        </span>
	</a>
<?php } ?>
