/* WooCommerce Products Wizard Data Table
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

    const pluginName = 'wcpwDataTable';
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
        this.$body = this.$element.children('table').children('tbody');
        this.$template = this.$element.children('fieldset').children('table').children('tbody')
            .children('[data-component~="wcpw-data-table-item"]');

        if ($.fn.sortable) {
            // init sortable
            this.$element.sortable({
                items: '[data-component~="wcpw-data-table-item"]',
                distance: 50,
                update: () => this.recalculateIds(),
                cancel: 'input,textarea,button,select,option,.wcpw-modal'
            });
        }

        return this.initEventListeners();
    };

    /**
     * Add required event listeners
     * @returns {this} self instance
     */
    Plugin.prototype.initEventListeners = function () {
        const _this = this;

        // add the form item
        this.$element.on('click', '[data-component~="wcpw-data-table-item-add"]', function () {
            return _this.addItem($(this).closest('[data-component~="wcpw-data-table-item"]'));
        });

        // remove the form item
        this.$element.on('click', '[data-component~="wcpw-data-table-item-remove"]', function () {
            return _this.removeItem($(this).closest('[data-component~="wcpw-data-table-item"]'));
        });

        // clone the form item
        this.$element.on('click', '[data-component~="wcpw-data-table-item-clone"]', function () {
            return _this.cloneItem($(this).closest('[data-component~="wcpw-data-table-item"]'));
        });

        return this;
    };

    /**
     * Add a new element in the table
     * @param {Object} $insertAfterItem - jQuery element
     * @param {Object} $template - jQuery element
     * @returns {Object} jQuery clone element
     */
    Plugin.prototype.addItem = function ($insertAfterItem, $template = this.$template) {
        $insertAfterItem = $insertAfterItem.length !== 0
            ? $insertAfterItem
            : this.$body.children('[data-component~="wcpw-data-table-item"]:last');

        if ($template.length === 0) {
            $template = $insertAfterItem;
        }

        // clone the new element from default template
        const $clone = $template.clone();
        
        $clone.find(':input').each(function () {
            // make real attributes from the placeholders
            $.each(this.attributes, (i, attr) => {
                const name = attr.name;
                const value = attr.value;

                if (name.indexOf('data-make') === -1) {
                    return this;
                }

                return this.setAttribute(name.replace('data-make-', ''), value);
            });
        });

        const $settingsModal = $clone.find('[data-component~="wcpw-data-table-item-modal"]');
        const $openSettingsModal = $clone.find('[data-component~="wcpw-data-table-item-open-modal"]');
        const rand = Math.random().toString(36).substr(2);

        $settingsModal.attr('id', $settingsModal.attr('id') + `-${rand.toString()}`);
        $openSettingsModal.attr('href', $openSettingsModal.attr('href') + `-${rand.toString()}`);

        // insert the clone element
        $clone.insertAfter($insertAfterItem);

        this.recalculateIds();
        this.$element.trigger('added.item.dataTable.wcpw', [this, $clone]);

        return $clone;
    };

    /**
     * Remove element from the table
     * @param {Object} $item - jQuery element
     * @returns {this} self instance
     */
    Plugin.prototype.removeItem = function ($item) {
        if ($item.is(':only-child')) {
            // clear values of the last item
            $item.find(':input').val('').trigger('change');
        } else {
            // remove the non-last item
            $item.remove();
        }

        this.recalculateIds();
        this.$element.trigger('removed.item.dataTable.wcpw', [this]);

        return this;
    };

    /**
     * Clone element from the table
     * @param {Object} $item - jQuery element
     * @returns {this} self instance
     */
    Plugin.prototype.cloneItem = function ($item) {
        const $clone = this.addItem($item, $item);

        this.$element.trigger('cloned.item.dataTable.wcpw', [this, $clone]);

        return this;
    };

    /**
     * Recalculate the input names indexes
     * @returns {this} self instance
     */
    Plugin.prototype.recalculateIds = function () {
        this.$body
            .children('[data-component~="wcpw-data-table-item"]')
            .each(function (index) {
                return $(this)
                    .find(':input')
                    .each(function () {
                        const name = this.getAttribute('name');

                        if (name) {
                            const number = index.toString().split('').reverse().join(''); // eslint-disable-line

                            // replace the first array key from the end
                            this.setAttribute(
                                'name',
                                name
                                    .split('')
                                    .reverse()
                                    .join('')
                                    .replace(/]\d+\[/, `]${number}[`)
                                    .split('')
                                    .reverse()
                                    .join('')
                            );
                        }
                    });
            });

        return this;
    };

    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, pluginName)) {
                $.data(this, pluginName, new Plugin(this, options));
            }
        });
    };

    const init = () => $('[data-component~="wcpw-data-table"]').each(function () {
        return $(this).wcpwDataTable();
    });

    $document.ready(() => init());
    $document.on('init.dataTable.wcpw', () => init());
});
