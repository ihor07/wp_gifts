<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Settings;
use WCProductsWizard\Template;
use WCProductsWizard\Utils;

$arguments = Template::getHTMLArgs([
    'id' => $id,
    'attachedProduct' => null,
    'formId' => null,
    'stepId' => null,
    'nextButtonText' => Settings::getPost($id, 'next_button_text'),
    'editCartItem' => null,
    'currentPageURL' => Utils::getCurrentURL()
]);

if (is_numeric($arguments['attachedProduct'])) {
    ?>
    <input type="hidden" form="<?php echo esc_attr($arguments['formId']); ?>" name="add-to-cart"
        value="<?php echo esc_attr($arguments['attachedProduct']); ?>">
    <input type="hidden" form="<?php echo esc_attr($arguments['formId']); ?>" name="attach-to-product"
        value="<?php echo esc_attr($arguments['attachedProduct']); ?>">
    <?php
} else {
    ?>
    <form action="#" method="POST" enctype="multipart/form-data" hidden
        id="<?php echo esc_attr($arguments['formId']); ?>" data-component="wcpw-form">
        <?php // no-js keyboard version of submit. should be upper the other ?>
        <button type="submit" class="sr-only visually-hidden" name="submit"
            form="<?php echo esc_attr($arguments['formId']); ?>"
            data-component="wcpw-next wcpw-nav-item" data-nav-action="submit"><?php
            echo wp_kses_post($arguments['nextButtonText']);
            ?></button>
    </form>
    <?php
}

if (isset($arguments['editCartItem']) && $arguments['editCartItem']) {
    ?>
    <input type="hidden" form="<?php echo esc_attr($arguments['formId']); ?>" name="editCartItem"
        value="<?php echo esc_attr($arguments['editCartItem']); ?>">
    <?php
}
?>
<input type="hidden" form="<?php echo esc_attr($arguments['formId']); ?>" name="woocommerce-products-wizard">
<input type="hidden" form="<?php echo esc_attr($arguments['formId']); ?>" name="id"
    value="<?php echo esc_attr($arguments['id']); ?>">
<input type="hidden" form="<?php echo esc_attr($arguments['formId']); ?>" name="currentPageURL"
    value="<?php echo esc_attr($arguments['currentPageURL']); ?>">
