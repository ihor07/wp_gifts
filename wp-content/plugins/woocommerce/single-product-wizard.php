<?php
<div class="accordion">
<div class="accordion-item">
  <div class="accordion-header">Расширенные настройки</div>
  <div class="accordion-content">
    <p>Содержимое аккордеона здесь...</p>
  </div>
</div>
</div>
<style>
.accordion {
    border: 1px solid #ccc;
    margin-bottom: 20px;
  }
  
  .accordion-item {
    border-bottom: 1px solid #ccc;
  }
  
  .accordion-header {
    background-color: #eee;
    color: #333;
    cursor: pointer;
    padding: 10px;
  }
  
  .accordion-content {
    display: none;
    padding: 10px;
  }
  </style>
function add_custom_price_field() {
  woocommerce_wp_text_input( array(
    'id' => '_custom_price_field',
    'label' => __( 'Custom Price', 'woocommerce' ),
    'desc_tip' => true,
    'description' => __( 'Enter the custom price for this product.', 'woocommerce' ),
    'value' => get_post_meta( get_the_ID(), '_custom_price_field', true ),
    'type' => 'number',
    'custom_attributes' => array(
      'step' => 'any',
      'min' => '0'
    )
  ) );
}
add_action( 'woocommerce_product_options_general_product_data', 'add_custom_price_field' );

// Save custom price field value
function save_custom_price_field( $post_id ) {
  $custom_price = $_POST['_custom_price_field'];
  if ( ! empty( $custom_price ) ) {
    update_post_meta( $post_id, '_custom_price_field', esc_attr( $custom_price ) );
  }
}
add_action( 'woocommerce_process_product_meta', 'save_custom_price_field' );

// Display custom price field in Product Wizard
function display_custom_price_field() {
  $custom_price = get_post_meta( get_the_ID(), '_custom_price_field', true );
  if ( ! empty( $custom_price ) ) {
    echo '<p><strong>' . __( 'Custom Price:', 'woocommerce' ) . '</strong> ' . wc_price( $custom_price ) . '</p>';
  }
}
add_action( 'woocommerce_single_product_summary', 'display_custom_price_field', 11 );
<script>
jQuery(document).ready(function($) {
    $('.accordion-header').click(function() {
      $(this).toggleClass('active').next('.accordion-content').slideToggle();
    });
  });  
</script>