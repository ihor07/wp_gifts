<?php
defined('ABSPATH') || exit;

$id = isset($id) ? $id : null;

if (!$id) {
    throw new Exception('Empty wizard id');
}

use WCProductsWizard\Form;
use WCProductsWizard\Template;
use WCProductsWizard\Settings;

$arguments = Template::getHTMLArgs([
    'stickyNav' => Settings::getPost($id, 'sticky_nav'),
    'stickyNavOffsetTop' => Settings::getPost($id, 'sticky_nav_offset_top'),
    'toggleMobileNavOn' => Settings::getPost($id, 'toggle_mobile_nav_on', 'post', 'sm'),
    'navItems' => Form::getNavItems($id),
    'formId' => null,
    'stepId' => null
]);

$isSticky = !in_array($arguments['stickyNav'], ['0', 'never']);
$class = ['is-step-' . $arguments['stepId']];

if ($isSticky) {
    $class[] = 'is-sticky' . (!in_array($arguments['stickyNav'], ['1', 'always']) ? "-{$arguments['stickyNav']}" : '');
}

if ($arguments['toggleMobileNavOn']) {
    $class[] = "d-none d-{$arguments['toggleMobileNavOn']}-block";
}
?>
<nav class="woocommerce-products-wizard-nav is-line-horizontal <?php echo esc_attr(implode(' ', $class))?>"<?php
    if ($isSticky && $arguments['stickyNavOffsetTop']) {
        if (is_int($arguments['stickyNavOffsetTop'])) {
            $arguments['stickyNavOffsetTop'] .= 'px';
        }

        echo ' style="top: ' . esc_attr($arguments['stickyNavOffsetTop']) . '"';
    }
    ?>
    data-component="wcpw-nav<?php echo $isSticky ? ' wcpw-sticky-observer' : ''; ?>">
    <ul class="woocommerce-products-wizard-nav-list is-line-horizontal" data-component="wcpw-nav-list" role="tablist">
        <?php
        foreach ($arguments['navItems'] as $navItem) {
            if (isset($navItem['merged_with_step']) && $navItem['merged_with_step']) {
                continue;
            }

            $thumbnail = $navItem['thumbnail']
                ? wp_get_attachment_image(
                    $navItem['thumbnail'],
                    'thumbnail',
                    false,
                    ['class' => 'woocommerce-products-wizard-nav-list-item-button-thumbnail']
                ) : '';
            ?>
            <li role="presentation"
                class="woocommerce-products-wizard-nav-list-item <?php echo esc_attr($navItem['class']); ?>">
                <button type="submit" role="tab"
                    form="<?php echo esc_attr($arguments['formId']); ?>"
                    name="<?php echo esc_attr($navItem['action']); ?>"
                    value="<?php echo esc_attr($navItem['value']); ?>"
                    class="woocommerce-products-wizard-nav-list-item-button <?php
                    echo esc_attr($navItem['class']);
                    echo $thumbnail ? ' has-thumbnail' : '';
                    ?>"
                    data-component="wcpw-nav-item"
                    data-nav-action="<?php echo esc_attr($navItem['action']); ?>"
                    data-nav-id="<?php echo esc_attr($navItem['value']); ?>"<?php
                    disabled($navItem['state'], 'disabled');
                    ?>><?php
                    echo $thumbnail ? $thumbnail . ' ' : '';
                    ?><span class="woocommerce-products-wizard-nav-list-item-button-inner"><?php
                        echo wp_kses_post($navItem['navName']);
                        ?></span></button>
            </li>
            <?php
        }
        ?>
    </ul>
</nav>
