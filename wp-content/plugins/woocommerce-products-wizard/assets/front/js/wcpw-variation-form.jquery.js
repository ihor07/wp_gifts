function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
/* WooCommerce Products Wizard product variation form handler
 * Original author: mail@troll-winner.ru
 * Further changes, comments: mail@troll-winner.ru
 */

(function (root, factory) {
  'use strict';

  if (typeof define === 'function' && define.amd) {
    define(['jquery'], factory);
  } else if ((typeof exports === "undefined" ? "undefined" : _typeof(exports)) === 'object' && typeof module !== 'undefined' && typeof require === 'function') {
    module.exports = factory(require('jquery'));
  } else {
    factory(root.jQuery);
  }
})(this, function ($) {
  'use strict';

  var pluginName = 'wcpwVariationForm';
  var Plugin = function Plugin(element) {
    var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
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
    var _this2 = this;
    var _this = this;

    // unbind any existing events
    this.$element.unbind('check_variations update_variation_values found_variation change');
    this.$element.off('.wc-variation-form');
    this.$input.unbind('change');

    // bind events
    // check variations
    this.$element.on('check_variations', function (event, exclude, focus) {
      var currentSettings = {};
      var allSet = true;
      _this2.$input.each(function () {
        var $element = $(this);
        if ($element.prop('tagName') === 'SELECT' && (!$element.val() || $element.val().length === 0)) {
          allSet = false;
        }
        if ($element.prop('tagName') === 'SELECT' || $element.is(':checked')) {
          currentSettings[this.getAttribute('data-name')] = $element.val();
        }
      });
      var matchingVariations = _this2.findMatchingVariations(_this2.$element.data('product_variations'), currentSettings);
      if (allSet) {
        var variation = null;
        for (var key in matchingVariations) {
          if (!matchingVariations.hasOwnProperty(key)) {
            continue;
          }
          var currentCopy = _this2.extendObject(currentSettings);
          var attributesCopy = _this2.extendObject(matchingVariations[key].attributes);
          for (var attributeCopyItem in attributesCopy) {
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
          _this2.$id.val(variation.variation_id).change();
          _this2.triggerEvent('found_variation', [variation]);
        } else if (!focus) {
          // Nothing found - reset fields
          _this2.triggerEvent('reset_image');
          _this2.triggerEvent('hide_variation');
        }
      } else {
        if (!focus) {
          _this2.triggerEvent('reset_image');
          _this2.triggerEvent('hide_variation');
        }
        if (!exclude) {
          // reset html
          _this2.$productPrice.html(_this2.$productPrice.data('default'));
          _this2.$productDescription.html(_this2.$productDescription.data('default'));
          _this2.$productAvailability.html(_this2.$productAvailability.data('default'));
          _this2.$productSku.html(_this2.$productSku.data('default'));
        }
      }
      _this2.triggerEvent('update_variation_values', [_this2.extendObject(matchingVariations), _this2.extendObject(currentSettings)]);

      // toggle add-to-cart controls according availability
      _this2.$productAddToCart.add(_this2.$productChoose).prop('disabled', !_this2.$id.val());
    });

    // reset product image
    this.$element.on('reset_image', function () {
      return _this2.updateImage(false);
    });

    // Disable option fields that are unavaiable for current set of attributes
    this.$element.on('update_variation_values', function (event, variations, currentSettings) {
      if (!variations || Object.keys(variations).length <= 0) {
        return _this2;
      }
      var isDefaultValue = true;

      // Loop through selects and disable/enable options based on selections
      _this2.$variationItem.each(function () {
        var $element = $(this);
        var currentAttrName = this.getAttribute('data-name');
        var $values = $element.find('[data-component~="wcpw-product-variations-item-value"]');
        $values.removeClass('active').prop('disabled', false);

        // Loop through variations
        for (var variationKey in variations) {
          if (!variations.hasOwnProperty(variationKey)) {
            continue;
          }
          var attributes = _this.extendObject(variations[variationKey].attributes);
          for (var attrName in attributes) {
            if (!attributes.hasOwnProperty(attrName) || attrName !== currentAttrName) {
              continue;
            }
            var attrVal = attributes[attrName];
            if (!attrVal) {
              var currentCopy = _this.extendObject(currentSettings);
              var attributesCopy = _this.extendObject(attributes);
              delete attributesCopy[attrName];
              delete currentCopy[attrName];
              for (var attributeCopyItem in attributesCopy) {
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
            $values.filter(attrVal !== '' ? "[value=\"".concat(attrVal, "\"]") : '*').addClass('active');
          }
        }

        // Detach inactive
        $values.filter(':not(.active)').prop('disabled', true);

        // choose a not-disabled value
        if ($element.prop('tagName') === 'SELECT') {
          var $activeValue = $element.find('option:selected');
          if (!$activeValue.attr('selected')) {
            isDefaultValue = false;
          }
          if ($activeValue.is(':disabled')) {
            var $otherValues = $element.find('option:not(:disabled)');
            if ($otherValues.length > 0) {
              // select first available value and init trigger
              // skip one tick to finish the current handler
              setTimeout(function () {
                return $element.val($otherValues.eq(0).attr('value')).trigger('change');
              }, 0);
            }
          }
        } else {
          var _$activeValue = $values.filter(':checked');
          if (!_$activeValue.attr('checked')) {
            isDefaultValue = false;
          }
          if (_$activeValue.is(':disabled')) {
            var _$otherValues = $values.filter(':not(:disabled)');
            if (_$otherValues.length > 0) {
              // select first available value and init trigger
              // skip one tick to finish the current handler
              setTimeout(function () {
                return _$otherValues.eq(0).prop('checked', true).trigger('change');
              }, 0);
            }
          }
        }
      });

      // show/hide reset button
      _this2.$reset.attr('hidden', isDefaultValue);

      // Custom event for when variations have been updated
      _this2.triggerEvent('woocommerce_update_variation_values');
      return _this2;
    });

    // show single variation details (price, stock, image)
    this.$element.on('found_variation', function (event, variation) {
      var purchasable = true;

      // change price
      if (variation.price_html) {
        _this2.$productPrice.html(variation.price_html);
      }

      // change min quantity
      if (variation.min_qty) {
        _this2.$productQuantity.attr('min', variation.min_qty);
      }

      // change max quantity
      if (variation.max_qty) {
        _this2.$productQuantity.attr('max', variation.max_qty);
      }

      // change description - support different versions of woocommerce
      _this2.$productDescription.html(variation.description || variation.variation_description || _this2.$productDescription.data('default'));

      // change availability
      _this2.$productAvailability.html(variation.availability_html || _this2.$productAvailability.data('default'));

      // change sku
      _this2.$productSku.html(variation.sku || _this2.$productSku.data('default'));

      // enable or disable the add to cart button and checkbox/radio
      if (!variation.is_purchasable || !variation.is_in_stock || !variation.variation_is_visible) {
        purchasable = false;
      }

      // toggle add-to-cart controls according availability
      _this2.$productAddToCart.add(_this2.$productChoose).prop('disabled', !purchasable);
      return _this2.updateImage(variation);
    });

    // reset form to default state
    this.$element.on('reset', function () {
      _this2.$variationItemValue.each(function () {
        var isInput = this.tagName === 'INPUT';
        return $(this).prop('disabled', false).prop(isInput ? 'checked' : 'selected', isInput ? this.defaultChecked : this.defaultSelected);
      });
      _this2.$reset.attr('hidden', true);
      _this2.triggerEvent('check_variations');
    });

    // upon changing an option
    this.$element.on('change.input.variationForm.wcpw', '[data-component~="wcpw-product-variations-item-input"]', function () {
      _this.$id.val('').change();
      _this.triggerEvent('woocommerce_variation_select_change');
      _this.triggerEvent('check_variations', [this.getAttribute('data-name'), true]);
      if ($().uniform && $.isFunction($.uniform.update)) {
        $.uniform.update();
      }
    });

    // reset button click event
    this.$element.on('click.reset.variationForm.wcpw', '[data-component~="wcpw-product-variations-reset"]', function (event) {
      event.preventDefault();
      return _this2.triggerEvent('reset');
    });
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
  Plugin.prototype.triggerEvent = function (name) {
    var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
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
    if (typeof element.attr("data-o_".concat(attr)) !== 'undefined') {
      element.attr(attr, element.attr("data-o_".concat(attr)));
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
    if (typeof element.attr("data-o_".concat(attr)) === 'undefined') {
      element.attr("data-o_".concat(attr), !element.attr(attr) ? '' : element.attr(attr));
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
    var output = [];
    var addedVariationsIds = {};
    for (var variationKey in productVariations) {
      if (!productVariations.hasOwnProperty(variationKey)) {
        continue;
      }
      var variation = productVariations[variationKey];
      for (var currentItem in current) {
        if (!current.hasOwnProperty(currentItem)) {
          continue;
        }
        var attributesCopy = this.extendObject(variation.attributes);
        var currentCopy = this.extendObject(current);

        // remove the same property from compare
        delete attributesCopy[currentItem];
        delete currentCopy[currentItem];
        for (var attributeCopyItem in attributesCopy) {
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
        if (JSON.stringify(attributesCopy) === JSON.stringify(currentCopy) && !addedVariationsIds.hasOwnProperty(variation.variation_id)) {
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
  Plugin.prototype.extendObject = function () {
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    return $.extend.apply($, [{}].concat(args));
  };
  $.fn[pluginName] = function (options) {
    return this.each(function () {
      if (!$.data(this, pluginName)) {
        $.data(this, pluginName, new Plugin(this, options));
      }
    });
  };
});