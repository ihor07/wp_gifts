/* WooCommerce Products Wizard product variation form handler
 * Original author: Alex Troll
 * Further changes, comments: mail@troll-winner.ru
 */

class WCPWVariationForm {
    constructor(element, options = {}) {
        this.element = element;
        this.customOptions = options;
        this.options = Object.assign({}, options);
    }
    
    /**
     * Init the instance
     * @returns {this} self instance
     */
    init() {
        this.eventListeners = [];
        this.$reset = this.element.querySelectorAll('[data-component~="wcpw-product-variations-reset"]');
        this.id = this.element.querySelector('[data-component~="wcpw-product-variations-variation-id"]');
        this.$input = this.element.querySelectorAll('[data-component~="wcpw-product-variations-item-input"]');
        this.$variationItem = this.element.querySelectorAll('[data-component~="wcpw-product-variations-item"]');
        this.$variationItemValue = this.element.querySelectorAll('[data-component~="wcpw-product-variations-item-value"]'); //eslint-disable-line
        this.product = this.element.closest('[data-component~="wcpw-product"]');
        this.$productPrice = this.product.querySelectorAll('[data-component~="wcpw-product-price"]');
        this.$productQuantity = this.product.querySelector('[data-component~="wcpw-product-quantity"] input:not([type="button"])'); //eslint-disable-line
        this.$productDescription = this.product.querySelectorAll('[data-component~="wcpw-product-description"]');
        this.$productAvailability = this.product.querySelectorAll('[data-component~="wcpw-product-availability"]');
        this.$productSku = this.product.querySelectorAll('[data-component~="wcpw-product-sku"]');
        this.$productAddToCart = this.product.querySelectorAll('[data-component~="wcpw-add-cart-product"]');
        this.$productChoose = this.product.querySelectorAll('[data-component~="wcpw-product-choose"]');
        this.$productImage = this.product.querySelectorAll('[data-component~="wcpw-product-thumbnail-image"]');
        this.$productLink = this.product.querySelectorAll('[data-component~="wcpw-product-thumbnail-link"]');

        return this.initEventListeners().triggerEvent('launched.variationForm.wcpw', {instance: this});
    }

    /**
     * Add required event listeners
     * @returns {this} self instance
     */
    initEventListeners() {
        const _this = this;

        // unbind any existing events
        this.unDelegateEventListener(
            'change.input.variationForm.wcpw',
            '[data-component~="wcpw-product-variations-item-input"]'
        );

        // bind events
        // check variations
        this.element.addEventListener('check_variations', (event) => {
            const currentSettings = {};
            let allSet = true;

            for (let element of this.$input) {
                if (element.tagName === 'SELECT' && (!element.value || element.value.length === 0)) {
                    allSet = false;
                }

                if (element.tagName === 'SELECT' || element.checked) {
                    currentSettings[element.getAttribute('data-name')] = element.value;
                }
            }

            let matchingVariations = this.findMatchingVariations(
                JSON.parse(this.element.getAttribute('data-product_variations') || '{}'),
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
                    this.id.value = variation.variation_id;
                    this.triggerEvent('found_variation', {variation});
                } else if (!event.detail.focus) {
                    // Nothing found - reset fields
                    this.triggerEvent('reset_image');
                    this.triggerEvent('hide_variation');
                }
            } else {
                if (!event.detail.focus) {
                    this.triggerEvent('reset_image');
                    this.triggerEvent('hide_variation');
                }

                if (!event.detail.exclude) {
                    // reset html
                    for (let element of this.$productPrice) {
                        element.innerHTML = element.getAttribute('data-default');
                    }

                    for (let element of this.$productDescription) {
                        element.innerHTML = element.getAttribute('data-default');
                    }

                    for (let element of this.$productAvailability) {
                        element.innerHTML = element.getAttribute('data-default');
                    }

                    for (let element of this.$productSku) {
                        element.innerHTML = element.getAttribute('data-default');
                    }
                }
            }

            this.triggerEvent(
                'update_variation_values',
                {
                    variations: this.extendObject(matchingVariations),
                    currentSettings: this.extendObject(currentSettings)
                }
            );

            // toggle add-to-cart controls according availability
            for (let element of this.$productAddToCart) {
                element.disabled = !this.id.value;
            }

            for (let element of this.$productChoose) {
                element.disabled = !this.id.value;
            }
        });

        // reset product image
        this.element.addEventListener('reset_image', () => this.updateImage(false));

        // Disable option fields that are unavaiable for current set of attributes
        this.element.addEventListener('update_variation_values', (event) => {
            let variations = event.detail.variation;

            if (!variations || Object.keys(variations).length <= 0) {
                return this;
            }

            let isDefaultValue = true;

            // Loop through selects and disable/enable options based on selections
            for (let element of this.$variationItem) {
                const currentAttrName = element.getAttribute('data-name');
                const $values = element.querySelectorAll('[data-component~="wcpw-product-variations-item-value"]');

                for (let value of $values) {
                    value.classList.remove('active');
                    value.disabled = false;
                }

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
                                for (let value of $values) {
                                    value.classList.add('active');
                                }
                            }
                        }

                        // Decode entities
                        attrVal = decodeURIComponent(attrVal);
                        // Add slashes
                        attrVal = attrVal.replace(/'/g, "\\'");
                        attrVal = attrVal.replace(/"/g, '\\\"');

                        // Compare the meerkat
                        for (let value of $values) {
                            if (attrVal) {
                                if (value.value === attrVal) {
                                    value.classList.add('active');
                                }
                            } else {
                                value.classList.add('active');
                            }
                        }
                    }
                }

                // Detach inactive
                for (
                    let value
                    of element.querySelectorAll('[data-component~="wcpw-product-variations-item-value"]:not(.active)')
                ) {
                    value.disabled = true;
                }

                // choose a not-disabled value
                if (element.tagName === 'SELECT') {
                    const activeValue = element.querySelector('option:checked');

                    if (!activeValue.getAttribute('selected')) {
                        isDefaultValue = false;
                    }

                    if (activeValue.disabled) {
                        const otherValue = element.querySelector('option:not(:disabled)');

                        if (otherValue) {
                            // select first available value
                            // skip one tick to finish the current handler
                            setTimeout(() => {
                                element.value = otherValue.value;
                            }, 0);
                        }
                    }
                } else {
                    const activeValue = element
                        .querySelector('[data-component~="wcpw-product-variations-item-value"]:checked');

                    if (!activeValue.getAttribute('checked')) {
                        isDefaultValue = false;
                    }

                    if (activeValue.disabled) {
                        const otherValue = element
                            .querySelector('[data-component~="wcpw-product-variations-item-value"]:not(:disabled)');

                        if (otherValue) {
                            // select first available value
                            // skip one tick to finish the current handler
                            setTimeout(() => {
                                otherValue.checked = true;
                            }, 0);
                        }
                    }
                }
            }

            // show/hide reset button
            for (let reset of this.$reset) {
                reset.setAttribute('hidden', String(isDefaultValue));
            }

            // Custom event for when variations have been updated
            this.triggerEvent('woocommerce_update_variation_values');

            return this;
        });

        // show single variation details (price, stock, image)
        this.element.addEventListener('found_variation', (event) => {
            let purchasable = true;
            let variation = event.detail.variation;

            // change price
            if (variation.price_html) {
                for (let element of this.$productPrice) {
                    element.innerHTML = variation.price_html;
                }
            }

            // change min quantity
            if (this.$productQuantity && variation.min_qty) {
                this.$productQuantity.setAttribute('min', variation.min_qty);
            }

            // change max quantity
            if (this.$productQuantity && variation.max_qty) {
                this.$productQuantity.setAttribute('max', variation.max_qty);
            }

            // change description - support different versions of woocommerce
            for (let element of this.$productDescription) {
                element.innerHTML = variation.description || variation.variation_description
                    || element.getAttribute('data-default');
            }

            // change availability
            for (let element of this.$productAvailability) {
                element.innerHTML = variation.availability_html || element.getAttribute('data-default');
            }

            // change sku
            for (let element of this.$productSku) {
                element.innerHTML = variation.sku || element.getAttribute('data-default');
            }

            // enable or disable the add to cart button and checkbox/radio
            if (!variation.is_purchasable || !variation.is_in_stock || !variation.variation_is_visible) {
                purchasable = false;
            }

            // toggle add-to-cart controls according availability
            for (let element of this.$productAddToCart) {
                element.disabled = !purchasable;
            }

            for (let element of this.$productChoose) {
                element.disabled = !purchasable;
            }

            return this.updateImage(variation);
        });

        // reset form to default state
        this.element.addEventListener('reset', () => {
            for (let element of this.$variationItemValue) {
                const isInput = element.tagName === 'INPUT';

                element.disabled = false;
                element.checked = isInput ? element.defaultChecked : element.defaultSelected;
            }

            for (let reset of this.$reset) {
                reset.setAttribute('hidden', true);
            }

            this.triggerEvent('check_variations');
        });

        // upon changing an option
        this.delegateEventListener(
            'change.input.variationForm.wcpw',
            '[data-component~="wcpw-product-variations-item-input"]',
            function () {
                _this.id.value = '';
                _this.triggerEvent('woocommerce_variation_select_change');
                _this.triggerEvent(
                    'check_variations',
                    {
                        exclude: this.getAttribute('data-name'),
                        focus: true
                    }
                );
            }
        );

        // reset button click event
        this.delegateEventListener(
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
    }

    /**
     * Delegate an event listener to a target
     * @param {String} action - event action name
     * @param {String} selector - target element selector
     * @param {Function} callback - function to fire
     * @param {Object} options - listener options
     * @returns {this} self instance
     */
    delegateEventListener(action, selector, callback, options = {capture: false}) {
        const actionName = action.split('.')[0];
        const handler = function (event) {
            let target = event.target;

            while (target && !target.matches(selector) && target !== this) {
                target = target.parentElement;
            }

            if (target && target.matches(selector)) {
                callback.call(target, event);
            }

            return this;
        };

        this.eventListeners.push({
            action,
            actionName,
            selector,
            handler
        });

        this.element.addEventListener(actionName, handler, options);

        return this;
    }

    /**
     * Un-delegate an event listener from the target
     * @param {String} action - event action name
     * @param {String} selector - target element selector
     * @param {Object} options - listener options
     * @returns {this} self instance
     */
    unDelegateEventListener(action, selector = '', options = {capture: false}) {
        const events = this.eventListeners
            .filter((item) => item.action === action && (!selector || item.selector === selector));

        if (events[0]) {
            this.element.removeEventListener(events[0].actionName, events[0].handler, options);
        }

        return this;
    }

    /**
     * Dispatch an event
     * @param {String} name - event name
     * @param {Object} options - object of arguments
     * @returns {this} self instance
     */
    triggerEvent(name, options = {}) {
        this.element.dispatchEvent(new CustomEvent(name, {bubbles: true, detail: options}));

        return this;
    }

    /**
     * Reset a default attribute for an element so it can be reset later
     * @param {Element} elements - query collection to work with
     * @param {String} attr - attribute name
     * @returns {this} self instance
     */
    resetAttr(elements, attr) {
        for (let element of elements) {
            if (element.getAttribute(`data-o_${attr}`)) {
                element.setAttribute(attr, element.getAttribute(`data-o_${attr}`));
            }
        }

        return this;
    }

    /**
     * Stores a default attribute for an element so it can be reset later
     * @param {Element} elements - query collection to work with
     * @param {String} attr - attribute name
     * @param {String} value - attribute value
     * @returns {this} self instance
     */
    setAttr(elements, attr, value) {
        for (let element of elements) {
            if (element.getAttribute(`data-o_${attr}`)) {
                element.setAttribute(`data-o_${attr}`, !element.getAttribute(attr) ? '' : element.getAttribute(attr));
            }

            if (value === false) {
                element.removeAttribute(attr);
            } else {
                element.setAttribute(attr, value);
            }
        }

        return this;
    }

    /**
     * Sets product images for the chosen variation
     * @param {Object} variation - variation data
     * @returns {this} self instance
     */
    updateImage(variation) {
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
    }

    /**
     * Get product matching variations
     * @param {Array} productVariations - variations collection
     * @param {Object} current - current properties object
     * @returns {Array} matching
     */
    findMatchingVariations(productVariations, current) {
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
    }

    /**
     * Extend object properties by other objects
     * @param {Object} args - object to extend
     * @returns {Object} new extended object
     */
    extendObject(...args) {
        return Object.assign({}, ...args);
    };
}

export default WCPWVariationForm;
