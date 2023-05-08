<?php
$args = isset($args) ? $args : [];
$modelItem = isset($modelItem) ? $modelItem : [];
$items = isset($args['values'][$modelItem['key']]) ? $args['values'][$modelItem['key']] : $modelItem['default'];
$items = empty($items) ? [[]] : $items;
$isSingleValue = count($modelItem['values']) <= 1;
$namespace = 'wcpw';
$textDomain = 'woocommerce-products-wizard';
$coreClass = isset($coreClass) ? $coreClass : '\WCProductsWizard\Admin';
$componentName = $namespace . '-data-table';
$modelItem['showHeader'] = isset($modelItem['inModal']) && $modelItem['inModal']
    ? false
    : (isset($modelItem['showHeader']) ? $modelItem['showHeader'] : true);

$rowCellTemplate = function (
    $modelItemValue,
    $modelItem,
    $itemKey,
    $asTemplate = false
) use (
    $args,
    $coreClass,
    $isSingleValue,
    $componentName,
    $namespace,
    $textDomain
) {
    $values = [];
    $namePattern = "$args[name][$itemKey]" . (!$isSingleValue ? "[$modelItemValue[key]]" : '');

    if (!$asTemplate && isset($args['values'][$modelItem['key']][$itemKey])) {
        if (!$isSingleValue) {
            $values = $args['values'][$modelItem['key']][$itemKey];
        } else {
            $values = [$modelItem['key'] => $args['values'][$modelItem['key']][$itemKey]];
        }
    }

    if (!method_exists($coreClass, 'settingFieldView')) {
        return;
    }

        $coreClass::settingFieldView(
            $modelItemValue,
            [
                'values' => $values,
                'namePattern' => $namePattern,
                'asTemplate' => $asTemplate,
                'generateId' => false
            ]
        );
};

$rowTemplate = function (
    $modelItem,
    $itemKey,
    $asTemplate = false
) use (
    $args,
    $namespace,
    $textDomain,
    $coreClass,
    $componentName,
    $rowCellTemplate
) {
    ?>
    <tr data-component="<?php echo esc_attr($componentName); ?>-item">
        <td class="<?php echo esc_attr($componentName); ?>-item-controls">
            <span class="button" role="button"
                data-component="<?php echo esc_attr($componentName); ?>-item-add">+</span>
        </td>
        <?php
        if (isset($modelItem['inModal']) && $modelItem['inModal']) {
            ?>
            <td>
                <a href="<?php echo esc_attr("#$componentName-$args[id]-modal" . (!$asTemplate ? "-$itemKey" : '')); ?>"
                    data-component="<?php echo esc_attr($componentName); ?>-item-open-modal"
                    class="button <?php echo esc_attr($componentName); ?>-item-open-modal"><?php
                    esc_html_e('Settings', $textDomain);
                    ?></a>
                <div id="<?php echo esc_attr("$componentName-$args[id]-modal" . (!$asTemplate ? "-$itemKey" : '')); ?>"
                    class="<?php echo esc_attr($namespace); ?>-modal"
                    data-component="<?php echo esc_attr($componentName); ?>-item-modal">
                    <div class="<?php echo esc_attr($namespace); ?>-modal-dialog">
                        <a href="#close" title="<?php esc_attr_e('Close', $textDomain); ?>"
                            class="<?php echo esc_attr($namespace); ?>-modal-close">&times;</a>
                        <div class="<?php echo esc_attr($namespace); ?>-modal-dialog-body">
                            <table class="form-table">
                                <?php foreach ($modelItem['values'] as $modelItemValue) { ?>
                                    <tr data-component="<?php echo esc_attr($componentName); ?>-body-item"
                                        data-key="<?php echo esc_attr($modelItemValue['key']); ?>">
                                        <th scope="row"><?php echo wp_kses_post($modelItemValue['label']); ?></th>
                                        <td><?php
                                            $rowCellTemplate($modelItemValue, $modelItem, $itemKey, $asTemplate);
                                            ?></td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                        <div class="<?php echo esc_attr($namespace); ?>-modal-dialog-footer">
                            <a href="#close" class="button button-primary"><?php
                                esc_html_e('Save', $textDomain);
                                ?></a>
                        </div>
                    </div>
                </div>
            </td>
            <?php
        } else {
            foreach ($modelItem['values'] as $modelItemValue) {
                ?>
                <td data-component="<?php echo esc_attr($componentName); ?>-body-item"
                    data-key="<?php echo esc_attr($modelItemValue['key']); ?>"><?php
                    $rowCellTemplate($modelItemValue, $modelItem, $itemKey, $asTemplate);
                    ?></td>
                <?php
            }
        }
        ?>
        <td width="10">
            <span role="button" data-component="<?php echo esc_attr($componentName); ?>-item-clone"
                class="button <?php echo esc_attr($componentName); ?>-item-clone"><?php
                esc_html_e('Clone', $textDomain);
                ?></span>
        </td>
        <td class="<?php echo esc_attr($componentName); ?>-item-controls">
            <span class="button" role="button"
                data-component="<?php echo esc_attr($componentName); ?>-item-remove">-</span>
        </td>
    </tr>
    <?php
};
?>
<div class="<?php echo esc_attr($componentName); ?>" data-component="<?php echo esc_attr($componentName); ?>">
    <table class="<?php echo esc_attr($componentName); ?>-main wp-list-table widefat striped"
        data-component="<?php echo esc_attr($componentName); ?>-main">
        <?php if (!isset($modelItem['showHeader']) || $modelItem['showHeader']) { ?>
            <thead>
                <tr>
                    <td><span class="screen-reader-text"><?php esc_html_e('Add', $textDomain); ?></span></td>
                    <?php foreach ($modelItem['values'] as $modelItemValue) { ?>
                        <td data-component="<?php echo esc_attr($componentName); ?>-header-item"
                            data-key="<?php echo esc_attr($modelItemValue['key']); ?>"><?php
                            echo wp_kses_post($modelItemValue['label']);
                            ?></td>
                    <?php } ?>
                    <td><span class="screen-reader-text"><?php esc_html_e('Clone', $textDomain); ?></span></td>
                    <td><span class="screen-reader-text"><?php esc_html_e('Remove', $textDomain); ?></span></td>
                </tr>
            </thead>
        <?php } ?>
        <tbody><?php
            foreach ($items as $itemKey => $_) {
                $rowTemplate($modelItem, $itemKey);
            }
            ?></tbody>
    </table>
    <fieldset hidden disabled><table><tbody><?php $rowTemplate($modelItem, 0, true); ?></tbody></table></fieldset>
</div>
