/* WooCommerce Products Wizard product variation form handler
 * Original author: Alex Troll
 * Further changes, comments: mail@troll-winner.ru
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

    const pluginName = 'wcpwVariationForm';
    const Plugin = function (element, options = {}) {
        this.element = element;
        this.customOptions = options;
        this.options = $.extend({}, options);
        this.init();
    };

    /**
     * Init the instance
     * @returns {this} self instance
     */
    Plugin.prototype.init = function () {
        this.$element = $(this.element);
        this.$reset = this.$element.find('[data-component~="wcpw-product-variations-reset"]');
        this.$id = this.$element.find('[data-component~="wcpw-product-variations-variation-id"]');
        this.$input = this.$element.find('[data-component~="wcpw-product-variations-item-input"]');
        this.$variationItem = this.$element.find('[data-component~="wcpw-product-variations-item"]');
        this.$variationItemValue = this.$element.find('[data-component~="wcpw-product-variations-item-value"]');
        this.$product = this.$element.closest('[data-component~="wcpw-product"]');
        this.$productPrice = this.$product.find('[data-component~="wcpw-product-price"]');
        this.$productQuantity = this.$product.find('[data-component~="wcpw-product-quantity"] :input:not([type="button"])'); // eslint-disable-line
        this.$productDescription = this.$product.find('[data-component~="wcpw-product-description"]');
        this.$productAvailability = this.$product.find('[data-component~="wcpw-product-availability"]');
        this.$productSku = this.$product.find('[data-component~="wcpw-product-sku"]');
        this.$productAddToCart = this.$product.find('[data-component~="wcpw-add-cart-product"]');
        this.$productChoose = this.$product.find('[data-component~="wcpw-product-choose"]');
        this.$productImage = this.$product.find('[data-component~="wcpw-product-thumbnail-image"]');
        this.$productLink = this.$product.find('[data-component~="wcpw-product-thumbnail-link"]');

        return this.initEventListeners().triggerEvent('launched.variationForm.wcpw', [this]);
    };

    /**
     * Add required event listeners
     * @returns {this} self instance
     */
    Plugin.prototype.initEventListeners = function () {
        const _this = this;

        // unbind any existing events
        this.$element.unbind('check_variations update_variation_values found_variation change');
        this.$element.off('.wc-variation-form');
        this.$input.unbind('change');

        // bind events
        // check variations
        this.$element.on('check_variations', (event, exclude, focus) => {
            const currentSettings = {};
            let allSet = true;

            this.$input.each(function () {
                const $element = $(this);

                if ($element.prop('tagName') === 'SELECT' && (!$element.val() || $element.val().length === 0)) {
                    allSet = false;
                }

                if ($element.prop('tagName') === 'SELECT' || $element.is(':checked')) {
                    currentSettings[this.getAttribute('data-name')] = $element.val();
                }
            });

            let matchingVariations = this.findMatchingVariations(
                this.$element.data('product_variations'),
                currentSettings
            );

            if (allSet) {
                let variation = null;

                for (let key in matchingVariations) {
                    if (!matchingVariations.hasOwnProperty(key)) {
                        continue;
                    }

                    const currentCopy = this.extendObject(currentSettings);
                    const attributesCopy = this.extendObject(matchingVariations[key].attributes);

                    for (let attributeCopyItem in attributesCopy) {
                        if (!attributesCopy.hasOwnProperty(attributeCopyItem)) {
                            continue;
                        }

                        // change "any" value to compare
                        if (attributesCopy[attributeCopyItem] === '') {
                            attributesCopy[attributeCopyItem] = currentCopy[attributeCopyItem];
                        }
                    }

                    // find the same variation as for the current properties
                    if (JSON.stringify(attributesCopy) === JSON.stringify(currentCopy)) {
                        variation = matchingVariations[key];

                        break;
                    }
                }

                if (variation) {
                    // Found - set ID
                    this.$id.val(variation.variation_id).change();
                    this.triggerEvent('found_variation', [variation]);
                } else if (!focus) {
                    // Nothing found - reset fields
                    this.triggerEvent('reset_image');
                    this.triggerEvent('hide_variation');
                }
            } else {
                if (!focus) {
                    this.triggerEvent('reset_image');
                    this.triggerEvent('hide_variation');
                }

                if (!exclude) {
                    // reset html
                    this.$productPrice.html(this.$productPrice.data('default'));
                    this.$productDescription.html(this.$productDescription.data('default'));
                    this.$productAvailability.html(this.$productAvailability.data('default'));
                    this.$productSku.html(this.$productSku.data('default'));
                }
            }

            this.triggerEvent(
                'update_variation_values',
                [this.extendObject(matchingVariations), this.extendObject(currentSettings)]
            );

            // toggle add-to-cart controls according availability
            this.$productAddToCart.add(this.$productChoose).prop('disabled', !this.$id.val());
        });

        // reset product image
        this.$element.on('reset_image', () => this.updateImage(false));

        // Disable option fields that are unavaiable for current set of attributes
        this.$element.on('update_variation_values', (event, variations, currentSettings) => {
            if (!variations || Object.keys(variations).length <= 0) {
                return this;
            }

            let isDefaultValue = true;

            // Loop through selects and disable/enable options based on selections
            this.$variationItem.each(function () {
                const $element = $(this);
                const currentAttrName = this.getAttribute('data-name');
                const $values = $element.find('[data-component~="wcpw-product-variations-item-value"]');

                $values.removeClass('active').prop('disabled', false);

                // Loop through variations
                for (let variationKey in variations) {
                    if (!variations.hasOwnProperty(variationKey)) {
                        continue;
                    }

                    const attributes = _this.extendObject(variations[variationKey].attributes);

                    for (let attrName in attributes) {
                        if (!attributes.hasOwnProperty(attrName) || attrName !== currentAttrName) {
                            continue;
                        }

                        let attrVal = attributes[attrName];

                        if (!attrVal) {
                            let currentCopy = _this.extendObject(currentSettings);
                            let attributesCopy = _this.extendObject(attributes);

                            delete attributesCopy[attrName];
                            delete currentCopy[attrName];

                            for (let attributeCopyItem in attributesCopy) {
                                if (!attributesCopy.hasOwnProperty(attributeCopyItem)) {
                                    continue;
                                }

                                // remove "any" values too
                                if (attributesCopy[attributeCopyItem] === '') {
                                    delete attributesCopy[attributeCopyItem];
                                    delete currentCopy[attributeCopyItem];
                                }
                            }

                            if (JSON.stringify(attributesCopy) === JSON.stringify(currentCopy)) {
                                $values.addClass('active');
                            }
                        }

                        // Decode entities
                        attrVal = $('<div/>').html(attrVal).text();
                        // Add slashes
                        attrVal = attrVal.replace(/'/g, "\\'");
                        attrVal = attrVal.replace(/"/g, '\\\"');
                        // Compare the meerkat
                        $values.filter(attrVal !== '' ? `[value="${attrVal}"]` : '*').addClass('active');
                    }
                }

                // Detach inactive
                $values.filter(':not(.active)').prop('disabled', true);

                // choose a not-disabled value
                if ($element.prop('tagName') === 'SELECT') {
                    const $activeValue = $element.find('option:selected');

                    if (!$activeValue.attr('selected')) {
                        isDefaultValue = false;
                    }

                    if ($activeValue.is(':disabled')) {
                        const $otherValues = $element.find('option:not(:disabled)');

                        if ($otherValues.length > 0) {
                            // select first available value and init trigger
                            // skip one tick to finish the current handler
                            setTimeout(() => $element.val($otherValues.eq(0).attr('value')).trigger('change'), 0);
                        }
                    }
                } else {
                    const $activeValue = $values.filter(':checked');

                    if (!$activeValue.attr('checked')) {
                        isDefaultValue = false;
                    }

                    if ($activeValue.is(':disabled')) {
                        const $otherValues = $values.filter(':not(:disabled)');

                        if ($otherValues.length > 0) {
                            // select first available value and init trigger
                            // skip one tick to finish the current handler
                            setTimeout(() => $otherValues.eq(0).prop('checked', true).trigger('change'), 0);
                        }
                    }
                }
            });

            // show/hide reset button
            this.$reset.attr('hidden', isDefaultValue);

            // Custom event for when variations have been updated
            this.triggerEvent('woocommerce_update_variation_values');

            return this;
        });

        // show single variation details (price, stock, image)
        this.$element.on('found_variation', (event, variation) => {
            let purchasable = true;

            // change price
            if (variation.price_html) {
                this.$productPrice.html(variation.price_html);
            }

            // change min quantity
            if (variation.min_qty) {
                this.$productQuantity.attr('min', variation.min_qty);
            }

            // change max quantity
            if (variation.max_qty) {
                this.$productQuantity.attr('max', variation.max_qty);
            }

            // change description - support different versions of woocommerce
            this.$productDescription.html(
                variation.description || variation.variation_description || this.$productDescription.data('default')
            );

            // change availability
            this.$productAvailability.html(variation.availability_html || this.$productAvailability.data('default'));

            // change sku
            this.$productSku.html(variation.sku || this.$productSku.data('default'));

            // enable or disable the add to cart button and checkbox/radio
            if (!variation.is_purchasable || !variation.is_in_stock || !variation.variation_is_visible) {
                purchasable = false;
            }

            // toggle add-to-cart controls according availability
            this.$productAddToCart.add(this.$productChoose).prop('disabled', !purchasable);

            return this.updateImage(variation);
        });

        // reset form to default state
        this.$element.on('reset', () => {
            this.$variationItemValue.each(function () {
                const isInput = this.tagName === 'INPUT';

                return $(this)
                    .prop('disabled', false)
                    .prop(
                        isInput ? 'checked' : 'selected',
                        isInput ? this.defaultChecked : this.defaultSelected
                    );
            });

            this.$reset.attr('hidden', true);
            this.triggerEvent('check_variations');
        });

        // upon changing an option
        this.$element.on(
            'change.input.variationForm.wcpw',
            '[data-component~="wcpw-product-variations-item-input"]',
            function () {
                _this.$id.val('').change();
                _this.triggerEvent('woocommerce_variation_select_change');
                _this.triggerEvent('check_variations', [this.getAttribute('data-name'), true]);

                if ($().uniform && $.isFunction($.uniform.update)) {
                    $.uniform.update();
                }
            }
        );

        // reset button click event
        this.$element.on(
            'click.reset.variationForm.wcpw',
            '[data-component~="wcpw-product-variations-reset"]',
            (event) => {
                event.preventDefault();

                return this.triggerEvent('reset');
            }
        );

        this.triggerEvent('check_variations');
        this.triggerEvent('wc_variation_form');

        return this;
    };

    /**
     * Dispatch an event
     * @param {String} name - event name
     * @param {Array} options - array of arguments
     * @returns {this} self instance
     */
    Plugin.prototype.triggerEvent = function (name, options = []) {
        this.$element.trigger(name, options);

        return this;
    };

    /**
     * Reset a default attribute for an element so it can be reset later
     * @param {Element} element - element to work with
     * @param {String} attr - attribute name
     * @returns {this} self instance
     */
    Plugin.prototype.resetAttr = function (element, attr) {
        if (typeof element.attr(`data-o_${attr}`) !== 'undefined') {
            element.attr(attr, element.attr(`data-o_${attr}`));
        }

        return this;
    };

    /**
     * Stores a default attribute for an element so it can be reset later
     * @param {Element} element - element to work with
     * @param {String} attr - attribute name
     * @param {String} value - attribute value
     * @returns {this} self instance
     */
    Plugin.prototype.setAttr = function (element, attr, value) {
        if (typeof element.attr(`data-o_${attr}`) === 'undefined') {
            element.attr(`data-o_${attr}`, !element.attr(attr) ? '' : element.attr(attr));
        }

        if (value === false) {
            element.removeAttr(attr);
        } else {
            element.attr(attr, value);
        }

        return this;
    };

    /**
     * Sets product images for the chosen variation
     * @param {Object} variation - variation data
     * @returns {this} self instance
     */
    Plugin.prototype.updateImage = function (variation) {
        if (variation && variation.image && (variation.image.src || variation.image_src)) {
            this.setAttr(this.$productImage, 'src', variation.image_src || variation.image.src);
            this.setAttr(this.$productImage, 'srcset', variation.image_srcset || variation.image.srcset);
            this.setAttr(this.$productImage, 'sizes', variation.image_sizes || variation.image.sizes);
            this.setAttr(this.$productImage, 'title', variation.image_title || variation.image.title);
            this.setAttr(this.$productImage, 'alt', variation.image_alt || variation.image.alt);
            this.setAttr(this.$productLink, 'href', variation.image_link || variation.image.full_src);
        } else {
            this.resetAttr(this.$productImage, 'src');
            this.resetAttr(this.$productImage, 'srcset');
            this.resetAttr(this.$productImage, 'sizes');
            this.resetAttr(this.$productImage, 'alt');
            this.resetAttr(this.$productLink, 'href');
        }

        return this;
    };

    /**
     * Get product matching variations
     * @param {Array} productVariations - variations collection
     * @param {Object} current - current properties object
     * @returns {Array} matching
     */
    Plugin.prototype.findMatchingVariations = function (productVariations, current) {
        const output = [];
        const addedVariationsIds = {};

        for (let variationKey in productVariations) {
            if (!productVariations.hasOwnProperty(variationKey)) {
                continue;
            }

            const variation = productVariations[variationKey];

            for (let currentItem in current) {
                if (!current.hasOwnProperty(currentItem)) {
                    continue;
                }

                let attributesCopy = this.extendObject(variation.attributes);
                let currentCopy = this.extendObject(current);

                // remove the same property from compare
                delete attributesCopy[currentItem];
                delete currentCopy[currentItem];

                for (let attributeCopyItem in attributesCopy) {
                    if (!attributesCopy.hasOwnProperty(attributeCopyItem)) {
                        continue;
                    }

                    // remove "any" values too
                    if (attributesCopy[attributeCopyItem] === '') {
                        delete attributesCopy[attributeCopyItem];
                        delete currentCopy[attributeCopyItem];
                    }
                }

                // if the other variation properties are the same as the current then allow this variation
                if (JSON.stringify(attributesCopy) === JSON.stringify(currentCopy)
                    && !addedVariationsIds.hasOwnProperty(variation.variation_id)
                ) {
                    addedVariationsIds[variation.variation_id] = variation.variation_id;
                    output.push(variation);
                }
            }
        }

        return output;
    };

    /**
     * Extend object properties by other objects
     * @param {Object} args - object to extend
     * @returns {Object} new extended object
     */
    Plugin.prototype.extendObject = function (...args) {
        return $.extend({}, ...args);
    };

    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, pluginName)) {
                $.data(this, pluginName, new Plugin(this, options));
            }
        });
    };
});
