<div <?php qode_framework_class_attribute( $holder_classes ); ?>>
	<?php if ( '' !== $open_table_id ) : ?>
		<form class="qodef-m-inner" target="_blank" action="https://www.opentable.com/restref/client/">
			<input type="hidden" name="rid" class="rid" value="<?php echo esc_attr( $open_table_id ); ?>">
			<input type="hidden" name="restref" class="restref" value="<?php echo esc_attr( $open_table_id ); ?>">

			<div class="qodef-m-field qodef-m-field-people">
				<select name="partysize" class="qodef-m-people">
					<option value="1"><?php esc_html_e( '1 Person', 'swissdelight-core' ); ?></option>
					<option value="2"><?php esc_html_e( '2 People', 'swissdelight-core' ); ?></option>
					<option value="3"><?php esc_html_e( '3 People', 'swissdelight-core' ); ?></option>
					<option value="4"><?php esc_html_e( '4 People', 'swissdelight-core' ); ?></option>
					<option value="5"><?php esc_html_e( '5 People', 'swissdelight-core' ); ?></option>
					<option value="6"><?php esc_html_e( '6 People', 'swissdelight-core' ); ?></option>
					<option value="7"><?php esc_html_e( '7 People', 'swissdelight-core' ); ?></option>
					<option value="8"><?php esc_html_e( '8 People', 'swissdelight-core' ); ?></option>
					<option value="9"><?php esc_html_e( '9 People', 'swissdelight-core' ); ?></option>
					<option value="10"><?php esc_html_e( '10 People', 'swissdelight-core' ); ?></option>
				</select>
                <?php echo qode_framework_icons()->render_icon( 'ion-md-people', 'ionicons', array( 'icon_attributes' => array( 'class' => 'qodef-m-icon ion-md-people' ) ) ); ?>
			</div>
			<div class="qodef-m-field qodef-m-field-date">
				<input type="text" value="<?php the_date( 'F j, Y' ); ?>" class="qodef-m-date" name="date">
                <?php echo qode_framework_icons()->render_icon( 'ion-md-calendar', 'ionicons', array( 'icon_attributes' => array( 'class' => 'qodef-m-icon ion-md-calendar' ) ) ); ?>
			</div>
			<div class="qodef-m-field qodef-m-field-time">
				<select name="time" class="qodef-m-time">
					<option value="00:30">00:30 am</option>
					<option value="01:00">01:00 am</option>
					<option value="01:30">01:30 am</option>
					<option value="02:00">02:00 am</option>
					<option value="02:30">02:30 am</option>
					<option value="03:00">03:00 am</option>
					<option value="03:30">03:30 am</option>
					<option value="04:00">04:00 am</option>
					<option value="04:30">04:30 am</option>
					<option value="05:00">05:00 am</option>
					<option value="05:30">05:30 am</option>
					<option value="06:00">06:00 am</option>
					<option value="06:30">06:30 am</option>
					<option value="07:00">07:00 am</option>
					<option value="07:30">07:30 am</option>
					<option value="08:00">08:00 am</option>
					<option value="08:30">08:30 am</option>
					<option value="09:00">09:00 am</option>
					<option value="09:30">09:30 am</option>
					<option value="10:00">10:00 am</option>
					<option value="10:30">10:30 am</option>
					<option value="11:00" selected>11:00 am</option>
					<option value="11:30">11:30 am</option>
					<option value="12:00">12:00 pm</option>
					<option value="12:30">12:30 pm</option>
					<option value="13:00">01:00 pm</option>
					<option value="13:30">01:30 pm</option>
					<option value="14:00">02:00 pm</option>
					<option value="14:30">02:30 pm</option>
					<option value="15:00">03:00 pm</option>
					<option value="15:30">03:30 pm</option>
					<option value="16:00">04:00 pm</option>
					<option value="16:30">04:30 pm</option>
					<option value="17:00">05:00 pm</option>
					<option value="17:30">05:30 pm</option>
					<option value="18:00">06:00 pm</option>
					<option value="18:30">06:30 pm</option>
					<option value="19:00">07:00 pm</option>
					<option value="19:30">07:30 pm</option>
					<option value="20:00">08:00 pm</option>
					<option value="20:30">08:30 pm</option>
					<option value="21:00">09:00 pm</option>
					<option value="21:30">09:30 pm</option>
					<option value="22:00">10:00 pm</option>
					<option value="22:30">10:30 pm</option>
					<option value="23:00">11:00 pm</option>
					<option value="23:30">11:30 pm</option>
					<option value="24:00">12:00 pm</option>
				</select>
                <?php echo qode_framework_icons()->render_icon( 'ion-md-time', 'ionicons', array( 'icon_attributes' => array( 'class' => 'qodef-m-icon ion-md-time' ) ) ); ?>
			</div>
			<div class="qodef-m-field qodef-m-field-book">
				<?php
				if ( class_exists( 'SwissDelightCore_Button_Shortcode' ) ) :
					$button_params['text']         = esc_html__( 'Book a Table', 'swissdelight-core' );
					$button_params['html_type']    = 'submit';
					$button_params['show_arrow']   = 'no';
					$button_params['custom_class'] = 'qodef-html--link';
					echo SwissDelightCore_Button_Shortcode::call_shortcode( $button_params );
				else :
					?>
					<input type="submit" class="qodef-btn qodef-btn-solid" name="qodef-rf-time">
				<?php endif; ?>
			</div>

			<input type="hidden" name="datetime" class="datetime" value="" />
		</form>
	<?php else : ?>
		<p><?php esc_html_e( 'You haven\'t added OpenTable ID', 'swissdelight-core' ); ?></p>
	<?php endif; ?>
</div>
