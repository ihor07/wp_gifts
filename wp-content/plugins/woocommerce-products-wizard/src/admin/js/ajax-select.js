/* WooCommerce Products Wizard Ajax Select
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

    const pluginName = 'wcpwAjaxSelect';
    const defaults = {};
    const ajaxRequests = {};
    const ajaxRequestsCache = {};
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
        this.$input = this.$element.next('[data-component~="wcpw-ajax-select-input"]');
        this.$target = false;
        this.ajaxUrl = this.$element.data('ajax-url');

        if (this.$element.data('target-parent')) {
            this.$target = this.$element.parents(this.$element.data('target-parent'));
        }

        if (this.$element.data('target-selector')) {
            if (this.$target) {
                this.$target = this.$target.find(this.$element.data('target-selector'));
            } else {
                this.$target = $(this.$element.data('target-selector'));
            }
        }

        // set items by default
        this.getItems().done((response) => {
            this.updateItems(response);

            if (this.$element.data('value')) {
                this.$element.val(this.$element.data('value'));
            }
        });

        this.$target
            .off('change.ajaxSelect.wcpw')
            .on('change.ajaxSelect.wcpw', () => this.getItems().done((response) => this.updateItems(response)));

        return this;
    };

    /**
     * Get items list via ajax
     * @returns {Promise} request
     */
    Plugin.prototype.getItems = function () {
        const action = this.$element.data('action');
        const value = this.$target.val();

        if (value === '' || typeof value === 'undefined') {
            return $.when('');
        }

        const data = {
            action,
            value
        };

        if (ajaxRequestsCache[action] && ajaxRequestsCache[action][value]) {
            return $.when(ajaxRequestsCache[action][value]);
        }

        if (ajaxRequests[action] && ajaxRequests[action][value]) {
            return ajaxRequests[action][value];
        }

        const request = $.ajax({
            url: this.ajaxUrl,
            data,
            dataType: 'json',
            success: (response) => {
                if (!ajaxRequestsCache[action]) {
                    ajaxRequestsCache[action] = {};
                }

                ajaxRequestsCache[action][value] = response;

                this.$element.trigger('updated.items.ajaxSelect.wcpw', [this, response]);
            }
        });

        if (!ajaxRequests[action]) {
            ajaxRequests[action] = {};
        }

        ajaxRequests[action][value] = request;

        return request;
    };

    /**
     * Update items list
     * @param {Object} data - args to pass
     * @returns {this} self instance
     */
    Plugin.prototype.updateItems = function (data) {
        this.$element.empty();

        if (data) {
            // output select options
            this.$element.attr('disabled', false).attr('hidden', false);
            this.$input.attr('disabled', true).attr('hidden', true);

            $.each(data, (value, name) => {
                this.$element.append(new Option(name, value, false, false));
            });
        } else {
            // output input field
            this.$element.attr('disabled', true).attr('hidden', true);
            this.$input.attr('disabled', false).attr('hidden', false);
        }

        this.$element.trigger('change');

        return this;
    };

    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, pluginName)) {
                $.data(this, pluginName, new Plugin(this, options));
            }
        });
    };

    const init = () => $('[data-component~="wcpw-ajax-select"]').each(function () {
        return $(this).wcpwAjaxSelect();
    });

    $document.ready(() => init());
    $document.on('init.ajaxSelect.wcpw', () => init());
});
