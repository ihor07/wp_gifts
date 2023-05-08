/* WooCommerce Products Wizard Multi-select
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

    const pluginName = 'wcpwMultiSelect';
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
        this.$availableItems = this.$element.find('[data-component~="wcpw-multi-select-items-available"]');
        this.$selectedItems = this.$element.find('[data-component~="wcpw-multi-select-items-selected"]');
        this.$inputs = this.$element.find('[data-component~="wcpw-multi-select-inputs"]');

        return this.initEventListeners();
    };

    /**
     * Add required event listeners
     * @returns {this} self instance
     */
    Plugin.prototype.initEventListeners = function () {
        // add the form item
        this.$element.on('click', '[data-component~="wcpw-multi-select-add"]', (event) => {
            event.preventDefault();

            return this.addItem();
        });

        // remove the form item
        this.$element.on('click', '[data-component~="wcpw-multi-select-remove"]', (event) => {
            event.preventDefault();

            return this.removeItem();
        });

        // move item upper
        this.$element.on('click', '[data-component~="wcpw-multi-select-move-up"]', (event) => {
            event.preventDefault();

            return this.moveItemUp();
        });

        // move item lower
        this.$element.on('click', '[data-component~="wcpw-multi-select-move-down"]', (event) => {
            event.preventDefault();

            return this.moveItemDown();
        });

        return this;
    };

    /**
     * Add the new element in the table
     * @returns {this} self instance
     */
    Plugin.prototype.addItem = function () {
        this.$availableItems
            .children(':selected')
            .each((i, selected) => {
                const $element = $(selected);

                $element.appendTo(this.$selectedItems);

                return this.$inputs
                    .children(`[value="${$element.val()}"]`)
                    .removeAttr('disabled');
            });

        this.$element.trigger('added.item.multiSelect.wcpw', [this]);

        return this;
    };

    /**
     * Remove element from the table
     * @returns {this} self instance
     */
    Plugin.prototype.removeItem = function () {
        this.$selectedItems
            .children(':selected')
            .each((i, selected) => {
                const $element = $(selected);

                $element.appendTo(this.$availableItems);

                return this.$inputs
                    .children(`[value="${$element.val()}"]`)
                    .attr('disabled', true);
            });

        this.$element.trigger('removed.item.multiSelect.wcpw', [this]);

        return this;
    };

    /**
     * Move element upper in list
     * @returns {this} self instance
     */
    Plugin.prototype.moveItemUp = function () {
        this.$selectedItems
            .children(':selected')
            .each((i, selected) => {
                const $element = $(selected);
                const $input = this.$inputs.children(`[value="${$element.val()}"]`);

                if ($element.prev().length <= 0) {
                    return this;
                }

                $element.insertBefore($element.prev());
                $input.insertBefore($input.prev());

                return this;
            });

        this.$element.trigger('movedUp.item.multiSelect.wcpw', [this]);

        return this;
    };

    /**
     * Move element lower in list
     * @returns {this} self instance
     */
    Plugin.prototype.moveItemDown = function () {
        $(this.$selectedItems
            .children(':selected')
            .get()
            .reverse())
            .each((i, selected) => {
                const $element = $(selected);
                const $input = this.$inputs.children(`[value="${$element.val()}"]`);

                if ($element.next().length <= 0) {
                    return this;
                }

                $element.insertAfter($element.next());
                $input.insertAfter($input.next());

                return this;
            });

        this.$element.trigger('movedDown.item.multiSelect.wcpw', [this]);

        return this;
    };

    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, pluginName)) {
                $.data(this, pluginName, new Plugin(this, options));
            }
        });
    };

    const init = () => $('[data-component~="wcpw-multi-select"]').each(function () {
        return $(this).wcpwMultiSelect();
    });

    $document.ready(() => init());
    $document.on('init.multiSelect.wcpw', () => init());
});
