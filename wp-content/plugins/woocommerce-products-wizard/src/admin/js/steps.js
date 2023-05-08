/* WooCommerce Products Wizard Steps
 * Original author: Alex Troll
 * Further changes, comments: mail@troll-winner.com
 */

(function (root, factory) {
    'use strict';

    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof exports === 'object' && typeof module !== 'undefined' && typeof require === 'function') {
        module.exports = factory(require('jquery'));
    } else {
        factory(root.jQuery);
    }
})(this, function ($) {
    'use strict';

    const pluginName = 'wcpwSteps';
    const defaults = {};
    const $document = $(document);

    const Plugin = function (element, options) {
        this.element = element;
        this.options = $.extend({}, defaults, options);

        return this.init();
    };

    /**
     * Init the instance
     * @returns {this} self instance
     */
    Plugin.prototype.init = function () {
        this.$element = $(this.element);
        this.$list = this.$element.find('[data-component~="wcpw-steps-list"]');
        this.$modal = $('[data-component~="wcpw-step-modal"]');
        this.$modalBody = this.$modal.find('[data-component~="wcpw-step-modal-body"]');
        this.ajaxUrl = this.element.getAttribute('data-ajax-url');

        if ($.fn.sortable) {
            // init sortable
            this.$list.sortable({
                items: '[data-component~="wcpw-steps-list-item"]',
                distance: 50
            });
        }

        return this;
    };

    /**
     * Add the new element
     * @returns {Object} jQuery new element
     */
    Plugin.prototype.addItem = function () {
        const $listChildren = this.$list.children();
        let id = 0;

        if ($listChildren.length) {
            $listChildren.each(function () {
                id = Math.max(id, Number(this.getAttribute('data-id')));
            });
        }

        id++;

        const $newItem = this.$element
            .find('[data-component~="wcpw-steps-list-item-template"]')
            .clone()
            .appendTo(this.$list)
            .attr('data-component', 'wcpw-steps-list-item')
            .attr('data-id', id);

        const $newItemSettings = $newItem.find('[data-component~="wcpw-steps-list-item-settings"]');
        const $newItemClone = $newItem.find('[data-component~="wcpw-steps-list-item-clone"]');

        $newItem
            .find('[data-component~="wcpw-steps-list-item-name"]')
            .text(`#${id}`)
            .end()
            .find('[data-component~="wcpw-steps-list-item-id"]')
            .attr('name', `_steps_ids[${id}]`)
            .attr('value', id);

        $newItemSettings.attr('data-settings', $newItemSettings.attr('data-settings').replace(/%STEP_ID%/g, id));
        $newItemClone.attr('data-settings', $newItemClone.attr('data-settings').replace(/%STEP_ID%/g, id));
        this.$element.trigger('added.item.steps.wcpw', [this, $newItemClone]);

        return $newItem;
    };

    /**
     * Show modal
     * @returns {this} self instance
     */
    Plugin.prototype.showModal = function () {
        this.$modal.addClass('is-opened');

        return this;
    };

    /**
     * Hide modal
     * @returns {this} self instance
     */
    Plugin.prototype.hideModal = function () {
        this.$modal.removeClass('is-opened');

        return this;
    };

    /**
     * Get step settings form
     * @param {Object} args - data
     * @returns {Promise} request
     */
    Plugin.prototype.getSettings = function (args) {
        const data = $.extend({}, {action: 'wcpwGetStepSettingsForm'}, args);

        return $.get(
            this.ajaxUrl,
            data,
            (response) => {
                // append data
                this.$modalBody.html(response);

                // filter label change
                this.$modalBody
                    .find(
                        '[data-component~="wcpw-data-table"][data-key="filters"]'
                        + ' [data-component="wcpw-data-table-body-item"][data-key="label"] input'
                    ).each(function () {
                        if (!this.value) {
                            return;
                        }

                        const $element = $(this);

                        $element.closest('[data-component="wcpw-data-table-item"]')
                            .find('[data-component="wcpw-data-table-item-open-modal"]')
                            .attr('data-name', $element.val());
                    });

                // show the modal
                this.showModal();
                this.$element.trigger('get.settings.item.steps.wcpw', [this, response]);
            }
        );
    };

    /**
     * Save step settings
     * @param {Object} $form - jQuery element
     * @returns {this} self instance
     */
    Plugin.prototype.saveSettings = function ($form) {
        const title = $form.find('#title').val();
        const notes = $form.find('#notes').val();
        const stepId = $form.attr('data-step-id');
        const data = {
            action: 'wcpwSaveStepSettings',
            post_id: $form.attr('data-post-id'),
            step_id: stepId,
            values: $form.serialize()
        };

        this.$list.find(`[data-component="wcpw-steps-list-item"][data-id="${stepId}"] `
            + `[data-component="wcpw-steps-list-item-name"]`)
            .html(`#${stepId} ${title}` + (notes ? ` <small>(${notes})</small>` : ''));

        return $.post(this.ajaxUrl, data, null, 'json');
    };

    /**
     * Clone step with settings
     * @param {Number} id - wizard id
     * @param {Number} sourceStep - source step id
     * @param {Number} targetStep - target step id
     * @returns {this} self instance
     */
    Plugin.prototype.cloneItem = function (id, sourceStep, targetStep) {
        const data = {
            action: 'wcpwCloneStepSettings',
            post_id: id,
            source_step: sourceStep,
            target_step: targetStep
        };

        return $.post(this.ajaxUrl, data, null, 'json');
    };

    /**
     * Remove element
     * @param {Object} $item - jQuery element
     * @returns {this} self instance
     */
    Plugin.prototype.removeItem = function ($item) {
        $item.remove();
        this.$element.trigger('removed.item.steps.wcpw', [this]);

        return this;
    };

    /**
     * Check HTML form validity
     * @param {Object} $form - jQuery object
     * @returns {Boolean} is valid
     */
    Plugin.prototype.checkFromValidity = function ($form) {
        let isValid = true;

        if ($form.length === 0) {
            return isValid;
        }

        $form.each(function () {
            if (this.checkValidity !== 'undefined') {
                isValid = this.checkValidity();
            }
        });

        return isValid;
    };

    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, pluginName)) {
                $.data(this, pluginName, new Plugin(this, options));
            }
        });
    };

    const init = () => $('[data-component~="wcpw-steps"]').each(function () {
        return $(this).wcpwSteps();
    });

    $document.ready(() => init());
    $document.on('init.steps.wcpw', () => init());

    // add form item
    $document.on('click', '[data-component~="wcpw-steps-add"]', function (event) {
        event.preventDefault();

        const $button = $(this);
        const $wcpwSteps = $button.closest('[data-component~="wcpw-steps"]');

        if ($wcpwSteps.data(pluginName)) {
            return $wcpwSteps.data(pluginName).addItem();
        }

        return this;
    });

    // remove form item
    $document.on('click', '[data-component~="wcpw-steps-list-item-remove"]', function (event) {
        event.preventDefault();

        const $button = $(this);
        const $wcpwSteps = $button.closest('[data-component~="wcpw-steps"]');

        if ($wcpwSteps.data(pluginName)) {
            return $wcpwSteps.data(pluginName)
                .removeItem($button.closest('[data-component~="wcpw-steps-list-item"]'));
        }

        return this;
    });

    // open settings modal
    $document.on('click', '[data-component~="wcpw-steps-list-item-settings"]', function (event) {
        event.preventDefault();

        const $button = $(this);
        const $wcpwSteps = $button.closest('[data-component~="wcpw-steps"]');

        $button.addClass('is-loading');

        if ($wcpwSteps.data(pluginName)) {
            return $wcpwSteps.data(pluginName)
                .getSettings($button.data('settings'))
                .always(() => $button.removeClass('is-loading'));
        }

        return this;
    });

    // clone step
    $document.on('click', '[data-component~="wcpw-steps-list-item-clone"]', function (event) {
        event.preventDefault();

        const $button = $(this);
        const $wcpwSteps = $button.closest('[data-component~="wcpw-steps"]');

        $button.addClass('is-loading');

        if ($wcpwSteps.data(pluginName)) {
            const $newItem = $wcpwSteps.data(pluginName).addItem();

            return $wcpwSteps.data(pluginName)
                .cloneItem(
                    $button.data('settings').post_id,
                    $button.data('settings').step_id,
                    $newItem.data('id')
                )
                .always(() => $button.removeClass('is-loading'));
        }

        return this;
    });

    // save the item settings
    $document.on('submit', '[data-component~="wcpw-step-settings-form"]', function (event) {
        const $form = $(this);
        const $wcpwSteps = $('[data-component~="wcpw-steps"]');

        if ($wcpwSteps.data(pluginName)) {
            if (!$wcpwSteps.data(pluginName).checkFromValidity($form)) {
                $form.find('[data-component~="wcpw-settings-group-content"]').attr('aria-expanded', 'true');

                return this;
            }

            event.preventDefault();

            $wcpwSteps.data(pluginName).hideModal().saveSettings($form);
        }

        return this;
    });

    // close modal
    $document.on('click', '[data-component~="wcpw-step-modal-close"]', function (event) {
        event.preventDefault();

        const $wcpwSteps = $('[data-component~="wcpw-steps"]');

        if ($wcpwSteps.data(pluginName)) {
            $wcpwSteps.data(pluginName).hideModal();
        }

        return this;
    });

    // change item template setting
    $document.on('change', '[data-component~="wcpw-step-settings-form"] #item_template', function () {
        const $itemTemplate = $(this);
        const $itemTemplatePreview = $itemTemplate.next('[data-component="wcpw-form-item-template-preview"]');

        $itemTemplatePreview
            .html(`<img alt="" src="${$itemTemplatePreview.data('src')}${$itemTemplate.val()}.png">`);
    });

    // apply to all steps checkbox click
    $document.on('change', '[data-component~="wcpw-step-setting-apply-to-all-input"]', function () {
        const $element = $(this);

        $element.closest('[data-component~="wcpw-step-setting-apply-to-all-label"]')[
            $element.is(':checked') ? 'addClass' : 'removeClass'
        ]('is-active');
    });

    // filter label change
    $document.on(
        'change',
        '[data-component~="wcpw-data-table"][data-key="filters"]'
        + ' [data-component="wcpw-data-table-body-item"][data-key="label"] input',
        function () {
            if (!this.value) {
                return;
            }

            const $element = $(this);

            $element.closest('[data-component="wcpw-data-table-item"]')
                .find('[data-component="wcpw-data-table-item-open-modal"]')
                .attr('data-name', $element.val());
        }
    );
});
