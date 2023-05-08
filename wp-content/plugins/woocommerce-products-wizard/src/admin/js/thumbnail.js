/* WooCommerce Products Wizard Thumbnail
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

    const pluginName = 'wcpwThumbnail';
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
        this.$id = this.$element.find('[data-component~="wcpw-thumbnail-id"]');
        this.$image = this.$element.find('[data-component~="wcpw-thumbnail-image"]');

        return this.initEventListeners();
    };

    /**
     * Add required event listeners
     * @returns {this} self instance
     */
    Plugin.prototype.initEventListeners = function () {
        // set thumbnail
        this.$element.on('click', '[data-component~="wcpw-thumbnail-set"]', (event) => {
            event.preventDefault();

            return this.openModal();
        });

        // remove thumbnail
        this.$element.on('click', '[data-component~="wcpw-thumbnail-remove"]', (event) => {
            event.preventDefault();

            return this.removeImage();
        });

        return this;
    };

    /**
     * Open thumbnail modal
     * @returns {this} self instance
     */
    Plugin.prototype.openModal = function () {
        // If the media frame already exists, reopen it.
        if (this.modalFrame) {
            this.modalFrame.open();

            return this;
        }

        // Create the media frame.
        this.modalFrame = wp.media.frames.downloadable_file = wp.media({
            title: 'Select image',
            button: {text: 'Select'},
            multiple: false
        });

        // When an image is selected, run a callback.
        this.modalFrame.on('select', () => {
            return this.modalFrame
                .state().get('selection')
                .map((attachment) => {
                    const attachmentJson = attachment.toJSON();

                    if (!attachmentJson.id) {
                        return null;
                    }

                    const src = {}.hasOwnProperty.call(attachmentJson, 'sizes')
                        && {}.hasOwnProperty.call(attachmentJson.sizes, 'thumbnail')
                        ? attachmentJson.sizes.thumbnail.url
                        : attachmentJson.url;

                    this.$image.html(`<img src="${src}">`);
                    this.$id.val(attachmentJson.id);
                    this.$element.trigger('selected.thumbnail.wcpw', [this, attachment]);

                    return attachment;
                });
        });

        // Finally, open the modal
        return this.modalFrame.open();
    };

    /**
     * Detach image is and remove image
     * @returns {this} self instance
     */
    Plugin.prototype.removeImage = function () {
        this.$image.html('');
        this.$id.val('');
        this.$element.trigger('removed.thumbnail.wcpw', [this]);

        return this;
    };

    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, pluginName)) {
                $.data(this, pluginName, new Plugin(this, options));
            }
        });
    };

    const init = () => $('[data-component~="wcpw-thumbnail"]').each(function () {
        return $(this).wcpwThumbnail();
    });

    $document.ready(() => init());
    $document.on('init.thumbnail.wcpw', () => init());
});
