<div <?php qode_framework_class_attribute( $holder_classes ); ?>>
	<span class="main-line" <?php echo qode_framework_get_inline_style( $line_styles ); ?>></span>

    <?php
    $i = 0;
    foreach ( $items as $item ) { ?>
        <div class="qodef-workflow-item">
            <span class="line"></span>
		    <?php
                $add_class = '';
		        $text_alignment = 'right';
                if($i % 2 == 0) {
                    $add_class .= ' reverse';
                    $text_alignment = 'left';
                }
		    ?>
            <div class="qodef-workflow-item-inner <?php echo esc_attr($add_class); ?>">
                <?php
                    $item_text_padding = array();
                    if( !empty($item['text_padding'])){
                        $item_text_padding[] = 'padding: '.$item['text_padding'];
                    }else{
                        $item_text_padding[] = '';
                    }
                    $item_text_padding = implode( ';', $item_text_padding );
                ?>
                <div class="qodef-workflow-text">
                    <span class="qodef-year">
                    <?php esc_html_e($item['year']); ?>
                </span>
                    <?php if(!empty($item['caption'])) {?>
                        <span class="qodef-m-caption"><?php esc_html_e($item['caption'])?></span>
                    <?php } ?>
                    <?php if(!empty($item['title'])) {?>
                        <h3 class="qodef-m-title"><?php esc_html_e($item['title'])?></h3>
                    <?php } ?>
                    <p class="qodef-m-text" <?php echo qode_framework_get_inline_style( $item_text_padding ); ?>><?php echo esc_attr($item['text']); ?></p>

                </div>
                <div class="qodef-workflow-image">
                    <div class="qodef-workflow-image-inner">
                        <?php if(!empty($item['image'])){
                            echo wp_get_attachment_image($item['image'], 'full');
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php
        $i++;
    } ?>
</div>
