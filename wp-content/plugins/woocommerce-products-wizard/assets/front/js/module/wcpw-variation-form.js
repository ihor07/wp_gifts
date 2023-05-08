function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"]; if (!it) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
/* WooCommerce Products Wizard product variation form handler
 * Original author: mail@troll-winner.ru
 * Further changes, comments: mail@troll-winner.ru
 */
var WCPWVariationForm = /*#__PURE__*/function () {
  function WCPWVariationForm(element) {
    var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    _classCallCheck(this, WCPWVariationForm);
    this.element = element;
    this.customOptions = options;
    this.options = Object.assign({}, options);
  }

  /**
   * Init the instance
   * @returns {this} self instance
   */
  _createClass(WCPWVariationForm, [{
    key: "init",
    value: function init() {
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
      return this.initEventListeners().triggerEvent('launched.variationForm.wcpw', {
        instance: this
      });
    }

    /**
     * Add required event listeners
     * @returns {this} self instance
     */
  }, {
    key: "initEventListeners",
    value: function initEventListeners() {
      var _this2 = this;
      var _this = this;

      // unbind any existing events
      this.unDelegateEventListener('change.input.variationForm.wcpw', '[data-component~="wcpw-product-variations-item-input"]');

      // bind events
      // check variations
      this.element.addEventListener('check_variations', function (event) {
        var currentSettings = {};
        var allSet = true;
        var _iterator = _createForOfIteratorHelper(_this2.$input),
          _step;
        try {
          for (_iterator.s(); !(_step = _iterator.n()).done;) {
            var _element4 = _step.value;
            if (_element4.tagName === 'SELECT' && (!_element4.value || _element4.value.length === 0)) {
              allSet = false;
            }
            if (_element4.tagName === 'SELECT' || _element4.checked) {
              currentSettings[_element4.getAttribute('data-name')] = _element4.value;
            }
          }
        } catch (err) {
          _iterator.e(err);
        } finally {
          _iterator.f();
        }
        var matchingVariations = _this2.findMatchingVariations(JSON.parse(_this2.element.getAttribute('data-product_variations') || '{}'), currentSettings);
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
            _this2.id.value = variation.variation_id;
            _this2.triggerEvent('found_variation', {
              variation: variation
            });
          } else if (!event.detail.focus) {
            // Nothing found - reset fields
            _this2.triggerEvent('reset_image');
            _this2.triggerEvent('hide_variation');
          }
        } else {
          if (!event.detail.focus) {
            _this2.triggerEvent('reset_image');
            _this2.triggerEvent('hide_variation');
          }
          if (!event.detail.exclude) {
            // reset html
            var _iterator2 = _createForOfIteratorHelper(_this2.$productPrice),
              _step2;
            try {
              for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
                var element = _step2.value;
                element.innerHTML = element.getAttribute('data-default');
              }
            } catch (err) {
              _iterator2.e(err);
            } finally {
              _iterator2.f();
            }
            var _iterator3 = _createForOfIteratorHelper(_this2.$productDescription),
              _step3;
            try {
              for (_iterator3.s(); !(_step3 = _iterator3.n()).done;) {
                var _element = _step3.value;
                _element.innerHTML = _element.getAttribute('data-default');
              }
            } catch (err) {
              _iterator3.e(err);
            } finally {
              _iterator3.f();
            }
            var _iterator4 = _createForOfIteratorHelper(_this2.$productAvailability),
              _step4;
            try {
              for (_iterator4.s(); !(_step4 = _iterator4.n()).done;) {
                var _element2 = _step4.value;
                _element2.innerHTML = _element2.getAttribute('data-default');
              }
            } catch (err) {
              _iterator4.e(err);
            } finally {
              _iterator4.f();
            }
            var _iterator5 = _createForOfIteratorHelper(_this2.$productSku),
              _step5;
            try {
              for (_iterator5.s(); !(_step5 = _iterator5.n()).done;) {
                var _element3 = _step5.value;
                _element3.innerHTML = _element3.getAttribute('data-default');
              }
            } catch (err) {
              _iterator5.e(err);
            } finally {
              _iterator5.f();
            }
          }
        }
        _this2.triggerEvent('update_variation_values', {
          variations: _this2.extendObject(matchingVariations),
          currentSettings: _this2.extendObject(currentSettings)
        });

        // toggle add-to-cart controls according availability
        var _iterator6 = _createForOfIteratorHelper(_this2.$productAddToCart),
          _step6;
        try {
          for (_iterator6.s(); !(_step6 = _iterator6.n()).done;) {
            var _element5 = _step6.value;
            _element5.disabled = !_this2.id.value;
          }
        } catch (err) {
          _iterator6.e(err);
        } finally {
          _iterator6.f();
        }
        var _iterator7 = _createForOfIteratorHelper(_this2.$productChoose),
          _step7;
        try {
          for (_iterator7.s(); !(_step7 = _iterator7.n()).done;) {
            var _element6 = _step7.value;
            _element6.disabled = !_this2.id.value;
          }
        } catch (err) {
          _iterator7.e(err);
        } finally {
          _iterator7.f();
        }
      });

      // reset product image
      this.element.addEventListener('reset_image', function () {
        return _this2.updateImage(false);
      });

      // Disable option fields that are unavaiable for current set of attributes
      this.element.addEventListener('update_variation_values', function (event) {
        var variations = event.detail.variation;
        if (!variations || Object.keys(variations).length <= 0) {
          return _this2;
        }
        var isDefaultValue = true;

        // Loop through selects and disable/enable options based on selections
        var _iterator8 = _createForOfIteratorHelper(_this2.$variationItem),
          _step8;
        try {
          var _loop = function _loop() {
            var element = _step8.value;
            var currentAttrName = element.getAttribute('data-name');
            var $values = element.querySelectorAll('[data-component~="wcpw-product-variations-item-value"]');
            var _iterator10 = _createForOfIteratorHelper($values),
              _step10;
            try {
              for (_iterator10.s(); !(_step10 = _iterator10.n()).done;) {
                var _value2 = _step10.value;
                _value2.classList.remove('active');
                _value2.disabled = false;
              }

              // Loop through variations
            } catch (err) {
              _iterator10.e(err);
            } finally {
              _iterator10.f();
            }
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
                    var _iterator11 = _createForOfIteratorHelper($values),
                      _step11;
                    try {
                      for (_iterator11.s(); !(_step11 = _iterator11.n()).done;) {
                        var value = _step11.value;
                        value.classList.add('active');
                      }
                    } catch (err) {
                      _iterator11.e(err);
                    } finally {
                      _iterator11.f();
                    }
                  }
                }

                // Decode entities
                attrVal = decodeURIComponent(attrVal);
                // Add slashes
                attrVal = attrVal.replace(/'/g, "\\'");
                attrVal = attrVal.replace(/"/g, '\\\"');

                // Compare the meerkat
                var _iterator12 = _createForOfIteratorHelper($values),
                  _step12;
                try {
                  for (_iterator12.s(); !(_step12 = _iterator12.n()).done;) {
                    var _value = _step12.value;
                    if (attrVal) {
                      if (_value.value === attrVal) {
                        _value.classList.add('active');
                      }
                    } else {
                      _value.classList.add('active');
                    }
                  }
                } catch (err) {
                  _iterator12.e(err);
                } finally {
                  _iterator12.f();
                }
              }
            }

            // Detach inactive
            var _iterator13 = _createForOfIteratorHelper(element.querySelectorAll('[data-component~="wcpw-product-variations-item-value"]:not(.active)')),
              _step13;
            try {
              for (_iterator13.s(); !(_step13 = _iterator13.n()).done;) {
                var _value3 = _step13.value;
                _value3.disabled = true;
              }

              // choose a not-disabled value
            } catch (err) {
              _iterator13.e(err);
            } finally {
              _iterator13.f();
            }
            if (element.tagName === 'SELECT') {
              var activeValue = element.querySelector('option:checked');
              if (!activeValue.getAttribute('selected')) {
                isDefaultValue = false;
              }
              if (activeValue.disabled) {
                var otherValue = element.querySelector('option:not(:disabled)');
                if (otherValue) {
                  // select first available value
                  // skip one tick to finish the current handler
                  setTimeout(function () {
                    element.value = otherValue.value;
                  }, 0);
                }
              }
            } else {
              var _activeValue = element.querySelector('[data-component~="wcpw-product-variations-item-value"]:checked');
              if (!_activeValue.getAttribute('checked')) {
                isDefaultValue = false;
              }
              if (_activeValue.disabled) {
                var _otherValue = element.querySelector('[data-component~="wcpw-product-variations-item-value"]:not(:disabled)');
                if (_otherValue) {
                  // select first available value
                  // skip one tick to finish the current handler
                  setTimeout(function () {
                    _otherValue.checked = true;
                  }, 0);
                }
              }
            }
          };
          for (_iterator8.s(); !(_step8 = _iterator8.n()).done;) {
            _loop();
          }

          // show/hide reset button
        } catch (err) {
          _iterator8.e(err);
        } finally {
          _iterator8.f();
        }
        var _iterator9 = _createForOfIteratorHelper(_this2.$reset),
          _step9;
        try {
          for (_iterator9.s(); !(_step9 = _iterator9.n()).done;) {
            var reset = _step9.value;
            reset.setAttribute('hidden', String(isDefaultValue));
          }

          // Custom event for when variations have been updated
        } catch (err) {
          _iterator9.e(err);
        } finally {
          _iterator9.f();
        }
        _this2.triggerEvent('woocommerce_update_variation_values');
        return _this2;
      });

      // show single variation details (price, stock, image)
      this.element.addEventListener('found_variation', function (event) {
        var purchasable = true;
        var variation = event.detail.variation;

        // change price
        if (variation.price_html) {
          var _iterator14 = _createForOfIteratorHelper(_this2.$productPrice),
            _step14;
          try {
            for (_iterator14.s(); !(_step14 = _iterator14.n()).done;) {
              var element = _step14.value;
              element.innerHTML = variation.price_html;
            }
          } catch (err) {
            _iterator14.e(err);
          } finally {
            _iterator14.f();
          }
        }

        // change min quantity
        if (_this2.$productQuantity && variation.min_qty) {
          _this2.$productQuantity.setAttribute('min', variation.min_qty);
        }

        // change max quantity
        if (_this2.$productQuantity && variation.max_qty) {
          _this2.$productQuantity.setAttribute('max', variation.max_qty);
        }

        // change description - support different versions of woocommerce
        var _iterator15 = _createForOfIteratorHelper(_this2.$productDescription),
          _step15;
        try {
          for (_iterator15.s(); !(_step15 = _iterator15.n()).done;) {
            var _element7 = _step15.value;
            _element7.innerHTML = variation.description || variation.variation_description || _element7.getAttribute('data-default');
          }

          // change availability
        } catch (err) {
          _iterator15.e(err);
        } finally {
          _iterator15.f();
        }
        var _iterator16 = _createForOfIteratorHelper(_this2.$productAvailability),
          _step16;
        try {
          for (_iterator16.s(); !(_step16 = _iterator16.n()).done;) {
            var _element8 = _step16.value;
            _element8.innerHTML = variation.availability_html || _element8.getAttribute('data-default');
          }

          // change sku
        } catch (err) {
          _iterator16.e(err);
        } finally {
          _iterator16.f();
        }
        var _iterator17 = _createForOfIteratorHelper(_this2.$productSku),
          _step17;
        try {
          for (_iterator17.s(); !(_step17 = _iterator17.n()).done;) {
            var _element9 = _step17.value;
            _element9.innerHTML = variation.sku || _element9.getAttribute('data-default');
          }

          // enable or disable the add to cart button and checkbox/radio
        } catch (err) {
          _iterator17.e(err);
        } finally {
          _iterator17.f();
        }
        if (!variation.is_purchasable || !variation.is_in_stock || !variation.variation_is_visible) {
          purchasable = false;
        }

        // toggle add-to-cart controls according availability
        var _iterator18 = _createForOfIteratorHelper(_this2.$productAddToCart),
          _step18;
        try {
          for (_iterator18.s(); !(_step18 = _iterator18.n()).done;) {
            var _element10 = _step18.value;
            _element10.disabled = !purchasable;
          }
        } catch (err) {
          _iterator18.e(err);
        } finally {
          _iterator18.f();
        }
        var _iterator19 = _createForOfIteratorHelper(_this2.$productChoose),
          _step19;
        try {
          for (_iterator19.s(); !(_step19 = _iterator19.n()).done;) {
            var _element11 = _step19.value;
            _element11.disabled = !purchasable;
          }
        } catch (err) {
          _iterator19.e(err);
        } finally {
          _iterator19.f();
        }
        return _this2.updateImage(variation);
      });

      // reset form to default state
      this.element.addEventListener('reset', function () {
        var _iterator20 = _createForOfIteratorHelper(_this2.$variationItemValue),
          _step20;
        try {
          for (_iterator20.s(); !(_step20 = _iterator20.n()).done;) {
            var element = _step20.value;
            var isInput = element.tagName === 'INPUT';
            element.disabled = false;
            element.checked = isInput ? element.defaultChecked : element.defaultSelected;
          }
        } catch (err) {
          _iterator20.e(err);
        } finally {
          _iterator20.f();
        }
        var _iterator21 = _createForOfIteratorHelper(_this2.$reset),
          _step21;
        try {
          for (_iterator21.s(); !(_step21 = _iterator21.n()).done;) {
            var reset = _step21.value;
            reset.setAttribute('hidden', true);
          }
        } catch (err) {
          _iterator21.e(err);
        } finally {
          _iterator21.f();
        }
        _this2.triggerEvent('check_variations');
      });

      // upon changing an option
      this.delegateEventListener('change.input.variationForm.wcpw', '[data-component~="wcpw-product-variations-item-input"]', function () {
        _this.id.value = '';
        _this.triggerEvent('woocommerce_variation_select_change');
        _this.triggerEvent('check_variations', {
          exclude: this.getAttribute('data-name'),
          focus: true
        });
      });

      // reset button click event
      this.delegateEventListener('click.reset.variationForm.wcpw', '[data-component~="wcpw-product-variations-reset"]', function (event) {
        event.preventDefault();
        return _this2.triggerEvent('reset');
      });
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
  }, {
    key: "delegateEventListener",
    value: function delegateEventListener(action, selector, callback) {
      var options = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {
        capture: false
      };
      var actionName = action.split('.')[0];
      var handler = function handler(event) {
        var target = event.target;
        while (target && !target.matches(selector) && target !== this) {
          target = target.parentElement;
        }
        if (target && target.matches(selector)) {
          callback.call(target, event);
        }
        return this;
      };
      this.eventListeners.push({
        action: action,
        actionName: actionName,
        selector: selector,
        handler: handler
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
  }, {
    key: "unDelegateEventListener",
    value: function unDelegateEventListener(action) {
      var selector = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
      var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {
        capture: false
      };
      var events = this.eventListeners.filter(function (item) {
        return item.action === action && (!selector || item.selector === selector);
      });
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
  }, {
    key: "triggerEvent",
    value: function triggerEvent(name) {
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      this.element.dispatchEvent(new CustomEvent(name, {
        bubbles: true,
        detail: options
      }));
      return this;
    }

    /**
     * Reset a default attribute for an element so it can be reset later
     * @param {Element} elements - query collection to work with
     * @param {String} attr - attribute name
     * @returns {this} self instance
     */
  }, {
    key: "resetAttr",
    value: function resetAttr(elements, attr) {
      var _iterator22 = _createForOfIteratorHelper(elements),
        _step22;
      try {
        for (_iterator22.s(); !(_step22 = _iterator22.n()).done;) {
          var element = _step22.value;
          if (element.getAttribute("data-o_".concat(attr))) {
            element.setAttribute(attr, element.getAttribute("data-o_".concat(attr)));
          }
        }
      } catch (err) {
        _iterator22.e(err);
      } finally {
        _iterator22.f();
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
  }, {
    key: "setAttr",
    value: function setAttr(elements, attr, value) {
      var _iterator23 = _createForOfIteratorHelper(elements),
        _step23;
      try {
        for (_iterator23.s(); !(_step23 = _iterator23.n()).done;) {
          var element = _step23.value;
          if (element.getAttribute("data-o_".concat(attr))) {
            element.setAttribute("data-o_".concat(attr), !element.getAttribute(attr) ? '' : element.getAttribute(attr));
          }
          if (value === false) {
            element.removeAttribute(attr);
          } else {
            element.setAttribute(attr, value);
          }
        }
      } catch (err) {
        _iterator23.e(err);
      } finally {
        _iterator23.f();
      }
      return this;
    }

    /**
     * Sets product images for the chosen variation
     * @param {Object} variation - variation data
     * @returns {this} self instance
     */
  }, {
    key: "updateImage",
    value: function updateImage(variation) {
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
  }, {
    key: "findMatchingVariations",
    value: function findMatchingVariations(productVariations, current) {
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
    }

    /**
     * Extend object properties by other objects
     * @param {Object} args - object to extend
     * @returns {Object} new extended object
     */
  }, {
    key: "extendObject",
    value: function extendObject() {
      for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }
      return Object.assign.apply(Object, [{}].concat(args));
    }
  }]);
  return WCPWVariationForm;
}();
export default WCPWVariationForm;