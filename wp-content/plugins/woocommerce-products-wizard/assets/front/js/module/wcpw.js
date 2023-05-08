function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"]; if (!it) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
/* WooCommerce Products Wizard app
 * Original author: mail@troll-winner.ru
 * Further changes, comments: mail@troll-winner.ru
 */
var WCPW = /*#__PURE__*/function () {
  // <editor-fold desc="Core">
  function WCPW(element) {
    var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    _classCallCheck(this, WCPW);
    var defaults = {
      documentNode: document,
      windowNode: window,
      rootSelector: 'html, body',
      searchStringMinLength: 3,
      ajaxActions: {
        submit: 'wcpwSubmit',
        addToMainCart: 'wcpwAddToMainCart',
        getStep: 'wcpwGetStep',
        skipStep: 'wcpwSkipStep',
        skipAll: 'wcpwSkipAll',
        submitAndSkipAll: 'wcpwSubmitAndSkipAll',
        reset: 'wcpwReset',
        addCartProduct: 'wcpwAddCartProduct',
        removeCartProduct: 'wcpwRemoveCartProduct',
        updateCartProduct: 'wcpwUpdateCartProduct',
        addCartStepData: 'wcpwAddCartStepData',
        removeCartStepData: 'wcpwRemoveCartStepData',
        search: 'wcpwSearch'
      }
    };
    this.element = element;
    this.customOptions = options;
    this.options = Object.assign({}, defaults, options);
  }

  /**
   * Init the instance
   * @returns {this} self instance
   */
  _createClass(WCPW, [{
    key: "init",
    value: function init() {
      this.hasError = false;
      this.preventAjaxRequest = false;
      this.productsWithError = [];
      this.ajaxRequestsQueue = [];
      this.eventListeners = [];
      return this.initEventListeners().triggerEvent('launched.wcpw', {
        instance: this
      });
    }

    /**
     * Makes an ajax-request
     * @param {FormData | Object} requestData - request data to pass
     * @param {Object} options - request options
     * @returns {Promise} ajax request
     */
  }, {
    key: "ajaxRequest",
    value: function ajaxRequest(requestData) {
      var _this2 = this;
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      this.preventAjaxRequest = false;
      var instance = this; // eslint-disable-line
      var formData = requestData instanceof FormData ? requestData : new FormData();
      var defaultOptions = {
        updateQueryArgs: true,
        queryArgs: {},
        currentQueryArgs: this.getQueryArgs(),
        cache: 'default',
        method: 'post',
        scrollingTopOnUpdate: Boolean(this.options.scrollingTopOnUpdate),
        scrollingGap: Number(this.options.scrollingUpGap),
        scrollingBehavior: 'smooth',
        scrollingToElement: null,
        passProducts: false,
        passStepData: false,
        errorsAlerting: true,
        errorsLogging: true,
        lazy: false
      };
      options = this.extendObject(defaultOptions, options);

      // pass extra args
      if (!(requestData instanceof FormData)) {
        for (var key in requestData) {
          if (requestData.hasOwnProperty(key)) {
            formData.append(key, typeof requestData[key] !== 'string' ? JSON.stringify(requestData[key]) : requestData[key]);
          }
        }
      }

      // remove stepsData fields to pass them right from the form as binary
      delete requestData.stepsData;

      // pass step data
      if (options.passStepData) {
        var form = this.element.querySelector('[data-component~="wcpw-form"]') || this.options.documentNode.querySelector('[data-component~="wcpw-form"]');
        var formElementData = new FormData(form);
        var stepDataToAddKey = formData.get('stepDataToAddKey');
        var _iterator = _createForOfIteratorHelper(formElementData.entries()),
          _step;
        try {
          for (_iterator.s(); !(_step = _iterator.n()).done;) {
            var pair = _step.value;
            if (pair[0].includes('stepsData') && (_typeof(pair[1]) === 'object' && pair[1].name || typeof pair[1] === 'string')) {
              // add only specific step data key
              if (stepDataToAddKey && pair[0] !== stepDataToAddKey) {
                continue;
              }
              formData.append(pair[0], pair[1]);
            }
          }
        } catch (err) {
          _iterator.e(err);
        } finally {
          _iterator.f();
        }
      }

      // don't pass products if needed
      if (!options.passProducts) {
        formData["delete"]('productsToAdd');
        formData["delete"]('productsToAddChecked');
      }

      // save extra parameters
      for (var _key in this.customOptions) {
        if (this.customOptions.hasOwnProperty(_key)) {
          formData.append(_key, typeof this.customOptions[_key] !== 'string' ? JSON.stringify(this.customOptions[_key]) : this.customOptions[_key]);
        }
      }

      // delete "add-to-cart" to not pass the attached product to the cart via AJAX
      formData["delete"]('add-to-cart');

      // add current query args to request
      if (options.currentQueryArgs.get) {
        // add extra query from "get" parameter
        if (options.currentQueryArgs.has('wcpwFilter')) {
          formData.append('wcpwFilter', options.currentQueryArgs.get('wcpwFilter'));
        }
        if (options.currentQueryArgs.has('wcpwPage')) {
          formData.append('wcpwPage', options.currentQueryArgs.get('wcpwPage'));
        }
        if (options.currentQueryArgs.has('wcpwProductsPerPage')) {
          formData.append('wcpwProductsPerPage', options.currentQueryArgs.get('wcpwProductsPerPage'));
        }
        if (options.currentQueryArgs.has('wcpwOrderBy')) {
          formData.append('wcpwOrderBy', options.currentQueryArgs.get('wcpwOrderBy'));
        }
      }

      // save passed query args to request
      if (options.queryArgs) {
        for (var _key2 in options.queryArgs) {
          if (options.queryArgs.hasOwnProperty(_key2)) {
            formData.append(_key2, options.queryArgs[_key2]);
          }
        }
      }
      this.triggerEvent('ajaxRequest.wcpw', {
        instance: instance,
        formData: formData,
        options: options
      });
      if (this.preventAjaxRequest) {
        this.triggerEvent('ajaxPrevent.wcpw', {
          instance: instance,
          formData: formData,
          options: options
        });
        return Promise.resolve();
      }
      this.element.classList.add(options.lazy ? 'is-lazy-loading' : 'is-loading');
      this.element.setAttribute('aria-live', 'polite');
      this.element.setAttribute('aria-busy', 'true');
      var request = this.options.windowNode.fetch(this.options.ajaxUrl, {
        method: options.method,
        cache: options.cache,
        body: formData
      }).then(function (response) {
        return response.json();
      }).then(function (response) {
        _this2.triggerEvent('ajaxSuccess.wcpw', {
          instance: instance,
          response: response,
          formData: formData,
          options: options
        });
        var requestIndex = _this2.ajaxRequestsQueue.indexOf(request);
        if (requestIndex > -1) {
          _this2.ajaxRequestsQueue.splice(requestIndex, 1);
        }
        if (options.lazy && _this2.ajaxRequestsQueue.length > 0) {
          return response;
        }
        _this2.element.classList.remove('is-lazy-loading', 'is-loading');
        _this2.element.setAttribute('aria-busy', 'false');
        if (response.content) {
          _this2.element.innerHTML = response.content;
        }

        // scroll navs
        var _iterator2 = _createForOfIteratorHelper(_this2.element.querySelectorAll('[data-component~="wcpw-nav"]')),
          _step2;
        try {
          for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
            var nav = _step2.value;
            var navList = nav.querySelector('[data-component~="wcpw-nav-list"]');
            if (navList) {
              navList.scrollLeft = nav.querySelector('.active').offsetLeft;
            }
          }

          // scroll window
        } catch (err) {
          _iterator2.e(err);
        } finally {
          _iterator2.f();
        }
        if (options.scrollingTopOnUpdate) {
          var element = _this2.element.querySelector(options.scrollingToElement || '[data-component~="wcpw-form-step"].is-active');
          if (element && !_this2.isScrolledIntoView(element)) {
            _this2.scrollToElement(element, Number(options.scrollingGap), options.scrollingBehavior);
          }
        }
        if (options.updateQueryArgs && options.queryArgs) {
          if (response.hasOwnProperty('stateHash')) {
            options.queryArgs.wcpwStateHash = response.stateHash;
          }
          if (response.hasOwnProperty('saveStateToURL') && Boolean(response.saveStateToURL)) {
            if (response.hasOwnProperty('id')) {
              options.queryArgs.wcpwId = response.id;
            }
            if (response.hasOwnProperty('stepId')) {
              options.queryArgs.wcpwStep = response.stepId;
            }
            if (response.hasOwnProperty('cart')) {
              options.queryArgs.wcpwCart = JSON.stringify(response.cart);
            }
          } else {
            options.queryArgs.wcpwStep = false;
          }
          if (Object.keys(options.queryArgs).filter(function (item) {
            return options.queryArgs[item] !== false;
          }).length !== 0) {
            _this2.setQueryArg(options.queryArgs);
          }
        }
        _this2.triggerEvent('ajaxCompleted.wcpw', {
          instance: instance,
          response: response,
          formData: formData,
          options: options
        });
        return response;
      })["catch"](function (error) {
        _this2.triggerEvent('ajaxError.wcpw', {
          instance: instance,
          error: error,
          formData: formData,
          options: options
        });
        var requestIndex = _this2.ajaxRequestsQueue.indexOf(request);
        if (requestIndex > -1) {
          _this2.ajaxRequestsQueue.splice(requestIndex, 1);
        }
        _this2.element.classList.remove('is-lazy-loading', 'is-loading');
        _this2.element.setAttribute('aria-busy', 'false');
        if (options.errorsLogging) {
          _this2.options.windowNode.console.error(error);
        }
        if (options.errorsAlerting) {
          _this2.options.windowNode.alert("Unexpected error occurred: ".concat(error));
        }
        return error;
      });
      this.ajaxRequestsQueue.push(request);
      return request;
    }

    /**
     * Send search request
     * @param {String} query - search text query
     * @param {String} targetSelector - results list selector
     * @param {Object} filterArgs - filter form request object
     * @returns {Promise} ajax query
     */
  }, {
    key: "search",
    value: function search(query, targetSelector, filterArgs) {
      if (!query || query.length < this.options.searchStringMinLength) {
        return Promise.resolve();
      }
      var target = this.element.querySelector(targetSelector);
      var template = target.getAttribute('data-item-template');
      var defaultData = {
        action: this.options.ajaxActions.search,
        id: this.options.id,
        query: query
      };
      return this.options.windowNode.fetch(this.options.ajaxUrl + '?' + this.objectToQueryParam(this.extendObject(defaultData, filterArgs))).then(function (response) {
        return response.json();
      }).then(function (response) {
        var html = [];
        if (response.items.length > 0) {
          for (var key in response.items) {
            if (!response.items.hasOwnProperty(key)) {
              continue;
            }
            var element = template;
            for (var property in response.items[key]) {
              if (response.items[key].hasOwnProperty(property)) {
                element = element.replace(new RegExp('\\${' + property + '}', 'g'), response.items[key][property]);
              }
            }
            html.push(element);
          }
        }
        target.innerHTML = html.join('');
        return response;
      });
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
     * Add required event listeners
     * @returns {this} self instance
     */
  }, {
    key: "initEventListeners",
    value: function initEventListeners() {
      var _this3 = this;
      var _this = this;

      // browser history handlers
      this.options.windowNode.addEventListener('popstate', function (event) {
        return _this3.popState(event);
      }, false);

      // prevent thumbnail link redirect on click
      this.delegateEventListener('click.thumbnail.product.wcpw', '[data-component~="wcpw-product-thumbnail-link"]', function (event) {
        return event.preventDefault();
      });

      // change the active form item
      this.delegateEventListener('click.product.wcpw', '[data-component~="wcpw-product"]', function () {
        var input = this.querySelector('[data-component~="wcpw-product-choose"][type="radio"]');
        if (input && !input.checked && !input.disabled) {
          input.checked = true;
          input.dispatchEvent(new Event('change'));
        }
      });

      // add product to the cart
      this.delegateEventListener('click.add.product.cart.wcpw', '[data-component~="wcpw-add-cart-product"]', function (event) {
        var _this4 = this;
        if (this.classList.contains('disabled')) {
          return event.preventDefault();
        }
        var otherInputs = [];
        var product = this.closest('[data-component~="wcpw-product"]');
        var inputs = _this.element.querySelectorAll('input:not(:disabled),select:not(:disabled),textarea:not(:disabled)');
        var _iterator3 = _createForOfIteratorHelper(inputs),
          _step3;
        try {
          for (_iterator3.s(); !(_step3 = _iterator3.n()).done;) {
            var _input2 = _step3.value;
            if (!product.contains(_input2)) {
              otherInputs.push(_input2);
            }
          }
        } catch (err) {
          _iterator3.e(err);
        } finally {
          _iterator3.f();
        }
        if (!_this.options.documentNode.querySelector('#' + this.getAttribute('form')).checkValidity()) {
          var _iterator4 = _createForOfIteratorHelper(otherInputs),
            _step4;
          try {
            for (_iterator4.s(); !(_step4 = _iterator4.n()).done;) {
              var input = _step4.value;
              input.disabled = false;
            }
          } catch (err) {
            _iterator4.e(err);
          } finally {
            _iterator4.f();
          }
          return this;
        }
        for (var _i = 0, _otherInputs = otherInputs; _i < _otherInputs.length; _i++) {
          var _input = _otherInputs[_i];
          _input.disabled = false;
        }
        this.classList.add('is-loading');
        this.setAttribute('aria-busy', 'true');
        event.preventDefault();
        return _this.addCartProduct({
          productToAddKey: this.value
        }, JSON.parse(this.getAttribute('data-add-cart-product-options') || '{}'))["finally"](function () {
          _this4.classList.remove('is-loading');
          _this4.setAttribute('aria-busy', 'false');
        });
      });

      // update product in the cart
      this.delegateEventListener('click.update.product.cart.wcpw', '[data-component~="wcpw-update-cart-product"]', function (event) {
        var _this5 = this;
        if (this.classList.contains('disabled')) {
          return event.preventDefault();
        }
        var otherInputs = [];
        var product = this.closest('[data-component~="wcpw-product"]');
        var inputs = _this.element.querySelectorAll('input:not(:disabled),select:not(:disabled),textarea:not(:disabled)');
        var _iterator5 = _createForOfIteratorHelper(inputs),
          _step5;
        try {
          for (_iterator5.s(); !(_step5 = _iterator5.n()).done;) {
            var _input6 = _step5.value;
            if (!product.contains(_input6)) {
              otherInputs.push(_input6);
            }
          }
        } catch (err) {
          _iterator5.e(err);
        } finally {
          _iterator5.f();
        }
        for (var _i2 = 0, _otherInputs2 = otherInputs; _i2 < _otherInputs2.length; _i2++) {
          var input = _otherInputs2[_i2];
          input.disabled = true;
        }
        for (var _i3 = 0, _otherInputs3 = otherInputs; _i3 < _otherInputs3.length; _i3++) {
          var _input3 = _otherInputs3[_i3];
          _input3.disabled = true;
        }
        if (!_this.options.documentNode.querySelector('#' + this.getAttribute('form')).checkValidity()) {
          var _iterator6 = _createForOfIteratorHelper(otherInputs),
            _step6;
          try {
            for (_iterator6.s(); !(_step6 = _iterator6.n()).done;) {
              var _input4 = _step6.value;
              _input4.disabled = false;
            }
          } catch (err) {
            _iterator6.e(err);
          } finally {
            _iterator6.f();
          }
          return this;
        }
        for (var _i4 = 0, _otherInputs4 = otherInputs; _i4 < _otherInputs4.length; _i4++) {
          var _input5 = _otherInputs4[_i4];
          _input5.disabled = false;
        }
        this.classList.add('is-loading');
        this.setAttribute('aria-busy', 'true');
        event.preventDefault();
        return _this.updateCartProduct({
          productCartKey: this.value
        }, JSON.parse(this.getAttribute('data-update-cart-product-options') || '{}'))["finally"](function () {
          _this5.classList.remove('is-loading');
          _this5.setAttribute('aria-busy', 'false');
        });
      });

      // remove product from the cart
      this.delegateEventListener('click.remove.product.cart.wcpw', '[data-component~="wcpw-remove-cart-product"]', function (event) {
        var _this6 = this;
        event.preventDefault();
        this.classList.add('is-loading');
        this.setAttribute('aria-busy', 'true');
        return _this.removeCartProduct({
          productCartKey: this.value
        }, JSON.parse(this.getAttribute('data-remove-cart-product-options') || '{}'))["finally"](function () {
          _this6.classList.remove('is-loading');
          _this6.setAttribute('aria-busy', 'false');
        });
      });

      // add step data to the cart
      this.delegateEventListener('change.add.stepData.cart.wcpw', '[data-component~="wcpw-add-cart-step-data"]', function (event) {
        var _this7 = this;
        if (this.classList.contains('disabled')) {
          return event.preventDefault();
        }
        event.preventDefault();
        this.classList.add('is-loading');
        this.setAttribute('aria-busy', 'true');
        return _this.addCartStepData({
          stepDataToAddKey: this.getAttribute('name')
        }, JSON.parse(this.getAttribute('data-add-cart-step-data-options') || '{}'))["finally"](function () {
          _this7.classList.remove('is-loading');
          _this7.setAttribute('aria-busy', 'false');
        });
      });

      // remove step data from the cart
      this.delegateEventListener('change.remove.stepData.cart.wcpw', '[data-component~="wcpw-remove-cart-step-data"]', function (event) {
        var _this8 = this;
        event.preventDefault();
        this.classList.add('is-loading');
        this.setAttribute('aria-busy', 'true');
        return _this.removeCartStepData({
          stepDataKey: this.getAttribute('data-name'),
          stepId: this.getAttribute('data-step-id')
        }, JSON.parse(this.getAttribute('data-remove-cart-step-data-options') || '{}'))["finally"](function () {
          _this8.classList.remove('is-loading');
          _this8.setAttribute('aria-busy', 'false');
        });
      });

      // nav item click
      this.delegateEventListener('click.nav.wcpw', '[data-component~="wcpw-nav-item"]', function (event) {
        var action = this.getAttribute('data-nav-action');
        var data = {
          action: action
        };
        if (!_this.options.documentNode.querySelector('#' + this.getAttribute('form')).checkValidity() && ['submit', 'add-to-main-cart', 'add-to-main-cart-repeat'].indexOf(action) !== -1) {
          return this;
        }
        event.preventDefault();
        if (this.getAttribute('data-nav-id')) {
          data.stepId = this.getAttribute('data-nav-id');
        }
        return _this.navRouter(data);
      });

      // share button click
      this.delegateEventListener('click.share.wcpw', '[data-component~="wcpw-share"]', function (event) {
        if (_this.setClipboard(this.getAttribute('href'))) {
          event.preventDefault();
          this.querySelector('*').innerText = this.getAttribute('data-share-success-message');
        }
        return this;
      });

      // filter submit
      this.delegateEventListener('submit.filter.wcpw', '[data-component~="wcpw-filter"]', function (event) {
        event.preventDefault();
        var queryArgs = _this.getQueryArgs();
        var filterData = _this.serializeObject(this).wcpwFilter;
        var stepId = this.getAttribute('data-step-id');
        var pages = {};
        var filters = {};
        if (queryArgs.get) {
          // change filter query
          if (queryArgs.has('wcpwFilter') && queryArgs.get('wcpwFilter')) {
            filters = _this.queryStringToObject(queryArgs.get('wcpwFilter'));
          }

          // reset page query
          if (queryArgs.has('wcpwPage') && queryArgs.get('wcpwPage')) {
            pages = _this.queryStringToObject(queryArgs.get('wcpwPage'));
            pages[stepId] = 1;
          }
        }
        filters = _this.extendObject(filters, filterData);
        return _this.getStep({
          stepId: stepId
        }, {
          queryArgs: {
            wcpwFilter: _this.objectToQueryParam(filters),
            wcpwPage: _this.objectToQueryParam(pages)
          }
        });
      });

      // filter reset
      this.delegateEventListener('reset.filter.wcpw', '[data-component~="wcpw-filter"]', function (event) {
        event.preventDefault();
        var queryArgs = _this.getQueryArgs();
        var filterData = _this.serializeObject(this).wcpwFilter;
        var stepId = this.getAttribute('data-step-id');
        var filters = {};
        if (queryArgs.get && queryArgs.has('wcpwFilter')) {
          filters = _this.queryStringToObject(queryArgs.get('wcpwFilter'));
        }
        filters = _this.extendObject(filters, _defineProperty({}, Object.keys(filterData)[0], {}));
        return _this.getStep({
          stepId: stepId
        }, {
          queryArgs: {
            wcpwFilter: _this.objectToQueryParam(filters)
          }
        });
      });

      // auto-submit form on change
      this.delegateEventListener('change.autoSubmit.wcpw', '[data-component~="wcpw-submit-on-change"]', function () {
        this.dispatchEvent(new Event('submit', {
          bubbles: true
        }));
      });

      // search form input change
      this.delegateEventListener('input.search.wcpw', '[data-component~="wcpw-search-form-input"]', function () {
        var _this9 = this;
        var filterData = _this.serializeObject(this.closest('[data-component~="wcpw-filter"]'));

        // clear previous timeout
        if (this.searchTimeout) {
          clearTimeout(this.searchTimeout);
        }

        // abort the current request
        if (this.searchPromise && this.searchPromise.abort) {
          this.searchPromise.abort();
        }

        // add extra step-id argument
        filterData.stepId = this.getAttribute('data-step-id');

        // attach a new request in a second
        this.searchTimeout = setTimeout(function () {
          _this9.classList.add('is-loading');
          _this9.setAttribute('aria-busy', 'true');
          _this9.searchPromise = _this.search(_this9.value, _this9.getAttribute('data-target'), filterData)["finally"](function () {
            _this9.classList.remove('is-loading');
            _this9.setAttribute('aria-busy', 'false');
          });
        }, 1000);
        return this.searchTimeout;
      });

      // search form results item click
      this.delegateEventListener('click.result.search.wcpw', '[data-component~="wcpw-search-form-results"] [data-value]', function (event) {
        event.preventDefault();
        var target = this.closest('[data-component~="wcpw-search-form-results"]').getAttribute('data-target');
        if (target) {
          _this.element.querySelector(target).value = this.getAttribute('data-value');
        }
        return this;
      });

      // pagination link click
      this.delegateEventListener('click.pagination.wcpw', '[data-component~="wcpw-form-pagination-link"]', function (event) {
        event.preventDefault();
        var queryArgs = _this.getQueryArgs();
        var stepId = this.getAttribute('data-step-id');
        var page = this.getAttribute('data-page');
        var pages = {};

        // change page query
        if (queryArgs.get && queryArgs.has('wcpwPage') && queryArgs.get('wcpwPage')) {
          pages = _this.queryStringToObject(queryArgs.get('wcpwPage'));
        }
        pages[stepId] = page;
        return _this.getStep({
          stepId: stepId,
          page: page
        }, {
          queryArgs: {
            wcpwPage: _this.objectToQueryParam(pages)
          },
          scrollingTopOnUpdate: true,
          scrollingToElement: "[data-component~=\"wcpw-form-step\"][data-id=\"".concat(stepId, "\"]")
        });
      });

      // products per page submit
      this.delegateEventListener('submit.productsPerPage.wcpw', '[data-component~="wcpw-form-products-per-page"]', function (event) {
        event.preventDefault();
        var queryArgs = _this.getQueryArgs();
        var value = _this.serializeObject(this);
        var stepId = this.getAttribute('data-step-id');
        var pages = {};
        var productsPerPage = {};
        if (queryArgs.get) {
          if (queryArgs.has('wcpwProductsPerPage')) {
            productsPerPage = _this.queryStringToObject(queryArgs.get('wcpwProductsPerPage'));
          }

          // reset page query
          if (queryArgs.has('wcpwPage') && queryArgs.get('wcpwPage')) {
            pages = _this.queryStringToObject(queryArgs.get('wcpwPage'));
            pages[stepId] = 1;
          }
        }
        productsPerPage[stepId] = value.wcpwProductsPerPage[stepId];
        return _this.getStep({
          stepId: stepId
        }, {
          queryArgs: {
            wcpwProductsPerPage: _this.objectToQueryParam(productsPerPage),
            wcpwPage: _this.objectToQueryParam(pages)
          }
        });
      });

      // products order-by submit
      this.delegateEventListener('submit.orderBy.wcpw', '[data-component~="wcpw-form-order-by"]', function (event) {
        event.preventDefault();
        var queryArgs = _this.getQueryArgs();
        var value = _this.serializeObject(this);
        var stepId = this.getAttribute('data-step-id');
        var orderBy = {};
        if (queryArgs.get && queryArgs.has('wcpwOrderBy')) {
          orderBy = _this.queryStringToObject(queryArgs.get('wcpwOrderBy'));
        }
        orderBy[stepId] = value.wcpwOrderBy[stepId];
        return _this.getStep({
          stepId: stepId
        }, {
          queryArgs: {
            wcpwOrderBy: _this.objectToQueryParam(orderBy)
          }
        });
      });

      // toggle element
      this.delegateEventListener('click.toggle.wcpw', '[data-component~="wcpw-toggle"]', function (event) {
        event.preventDefault();
        var targetSelector = this.getAttribute('data-target') || this.getAttribute('href');
        var target = _this.element.querySelector(targetSelector);
        var isClosed = target.getAttribute('aria-expanded') === 'false';
        this.setAttribute('aria-expanded', isClosed ? 'true' : 'false');
        target.setAttribute('aria-expanded', isClosed ? 'true' : 'false');
        _this.options.documentNode.cookie = "".concat(targetSelector, "-expanded=").concat(String(isClosed), "; path=/");
        _this.triggerEvent('toggle.wcpw', {
          instance: _this,
          target: target
        });
        return this;
      });
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
     * Get a step by the previous state
     * @param {PopStateEvent} event - window history pop event
     * @returns {Promise} ajax request
     */
  }, {
    key: "popState",
    value: function popState(event) {
      var requestArgs = {
        action: this.options.ajaxActions.getStep
      };
      var openingPath = event && event.state && event.state.path;
      if (!openingPath) {
        return Promise.resolve();
      }
      var queryArgs = new URL(openingPath).searchParams;
      if (queryArgs.has('wcpwStep')) {
        requestArgs.stepId = queryArgs.get('wcpwStep');
      }
      if (queryArgs.has('wcpwFilters')) {
        requestArgs.filters = this.queryStringToObject(queryArgs.get('wcpwFilters'))[requestArgs.stepId];
      }
      if (queryArgs.has('wcpwPages')) {
        requestArgs.page = this.queryStringToObject(queryArgs.get('wcpwPages'))[requestArgs.stepId];
      }
      if (queryArgs.has('wcpwOrderBy')) {
        requestArgs.orderby = this.queryStringToObject(queryArgs.get('wcpwOrderBy'))[requestArgs.stepId];
      }
      return this.ajaxRequest(requestArgs, {
        updateQueryArgs: false
      });
    }
  }, {
    key: "addCartProduct",
    value:
    // </editor-fold>

    // <editor-fold desc="Product actions">
    /**
     * Add form product to the cart
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
    function addCartProduct() {
      var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      var defaultOptions = {
        behavior: 'default',
        passProducts: true,
        passStepData: false
      };
      data = this.extendObject({
        action: this.options.ajaxActions.addCartProduct
      }, data);
      options = this.extendObject(defaultOptions, options);

      // change the action to submit
      switch (options.behavior) {
        default:
        case 'default':
          return this.submit(data, this.extendObject({
            scrollingTopOnUpdate: false
          }, options));
        case 'submit':
          return this.submit(data, options);
        case 'add-to-main-cart':
          return this.addToMainCart(data, options);
      }
    }

    /**
     * Update form product in the cart
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
  }, {
    key: "updateCartProduct",
    value: function updateCartProduct() {
      var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      var defaultOptions = {
        behavior: 'default',
        passProducts: true,
        passStepData: false
      };
      data = this.extendObject({
        action: this.options.ajaxActions.updateCartProduct
      }, data);
      options = this.extendObject(defaultOptions, options);

      // change the action to submit
      switch (options.behavior) {
        default:
        case 'default':
          return this.submit(data, this.extendObject({
            scrollingTopOnUpdate: false
          }, options));
        case 'submit':
          return this.submit(data, options);
        case 'add-to-main-cart':
          return this.addToMainCart(data, options);
      }
    }

    /**
     * Remove form product from the cart
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
  }, {
    key: "removeCartProduct",
    value: function removeCartProduct() {
      var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      var defaultOptions = {
        scrollingTopOnUpdate: false,
        passProducts: true
      };
      data = this.extendObject({
        action: this.options.ajaxActions.removeCartProduct
      }, data);
      options = this.extendObject(defaultOptions, options);

      // make custom request instead of the form submit
      return this.ajaxRequest(data, options);
    }
    // </editor-fold>

    // <editor-fold desc="Step data actions">
    /**
     * Add form step data to the cart
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
  }, {
    key: "addCartStepData",
    value: function addCartStepData() {
      var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      var defaultOptions = {
        behavior: 'default',
        passProducts: false,
        passStepData: true
      };
      options = this.extendObject(defaultOptions, options);

      // change the action to submit
      switch (options.behavior) {
        default:
        case 'default':
          return this.submit(this.extendObject({
            action: this.options.ajaxActions.addCartStepData
          }, data), this.extendObject({
            scrollingTopOnUpdate: false
          }, options));
        case 'submit':
          return this.submit(data, options);
        case 'add-to-main-cart':
          return this.addToMainCart(data, options);
      }
    }

    /**
     * Remove form step data from the cart
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
  }, {
    key: "removeCartStepData",
    value: function removeCartStepData() {
      var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      data = this.extendObject({
        action: this.options.ajaxActions.removeCartStepData
      }, data);
      options = this.extendObject({
        scrollingTopOnUpdate: false
      }, options);

      // make custom request instead of the form submit
      return this.ajaxRequest(data, options);
    }
    // </editor-fold>

    // <editor-fold desc="Main actions">
    /**
     * Add selected products to the main cart
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
  }, {
    key: "addToMainCart",
    value: function addToMainCart() {
      var _this10 = this;
      var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      var instance = this; // eslint-disable-line
      var defaultOptions = {
        preventRedirect: false,
        passProducts: true,
        passStepData: true
      };
      data = this.extendObject({
        action: this.options.ajaxActions.addToMainCart
      }, data);
      options = this.extendObject(defaultOptions, options);
      var result = this.submit(data, options);
      this.triggerEvent('addToMainCart.wcpw', {
        instance: instance,
        data: data,
        result: result
      });
      if (!result) {
        return Promise.resolve();
      }
      return result.then(function (response) {
        // has some product errors
        if (response.hasError || _this10.hasError) {
          _this10.triggerEvent('addToMainCartError.wcpw', {
            instance: instance,
            data: data,
            response: response
          });
          return response;
        }
        if (!options.preventRedirect && !response.preventRedirect && response.finalRedirectUrl) {
          _this10.triggerEvent('addToMainCartRedirect.wcpw', {
            instance: instance,
            data: data,
            response: response
          });
          _this10.options.documentNode.location = response.finalRedirectUrl;
        }
        return response;
      });
    }

    /**
     * Add selected products to the main cart and repeat the workflow
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
  }, {
    key: "addToMainCartRepeat",
    value: function addToMainCartRepeat() {
      var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      data.getContent = true;
      options.preventRedirect = true;
      return this.addToMainCart(data, options);
    }

    /**
     * Send custom products from the active step to the wizard cart
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request or false
     */
  }, {
    key: "submit",
    value: function submit() {
      var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      // reset error state
      this.hasError = false;
      this.productsWithError = [];
      var instance = this;
      var form = this.element.querySelector('[data-component~="wcpw-form"]') || this.options.documentNode.querySelector('[data-component~="wcpw-form"]');
      var formData = this.serializeObject(form);
      var defaultData = {
        action: this.options.ajaxActions.submit,
        productToAddKey: null,
        productsToAdd: [],
        productsToAddChecked: []
      };
      var defaultOptions = {
        passProducts: true,
        passStepData: true
      };
      data = this.extendObject(defaultData, data, formData);
      options = this.extendObject(defaultOptions, options);
      if (data.productToAddKey) {
        // keep only one product by id
        for (var key in data.productsToAdd) {
          if (data.productsToAdd.hasOwnProperty(key)) {
            var product = data.productsToAdd[key];
            if ("".concat(product.step_id, "-").concat(product.product_id) !== data.productToAddKey) {
              delete data.productsToAdd[key];
            } else {
              data.productsToAddChecked = _defineProperty({}, product.step_id, [product.product_id]);
            }
          }
        }
      } else {
        delete data.productToAddKey;
      }
      this.triggerEvent('submit.wcpw', {
        instance: instance,
        data: data
      });

      // has some errors
      if (this.hasError) {
        this.triggerEvent('submitError.wcpw', {
          instance: instance,
          data: data
        });
        return Promise.resolve();
      }

      // send ajax
      return this.ajaxRequest(data, options);
    }

    /**
     * Route to the required navigation event
     * @param {Object} args - object of arguments
     * @param {Object} options - object of method options
     * @returns {Object} nav function
     */
  }, {
    key: "navRouter",
    value: function navRouter() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      var action = args.action;

      // action will be added by a method
      delete args.action;
      switch (action) {
        case 'skip-step':
          return this.skipStep(args, options);
        case 'skip-all':
          return this.skipAll(args, options);
        case 'submit-and-skip-all':
          return this.submitAndSkipAll(args, options);
        case 'submit':
          return this.submit(args, options);
        case 'add-to-main-cart':
          return this.addToMainCart(args, options);
        case 'add-to-main-cart-repeat':
          return this.addToMainCartRepeat(args, options);
        case 'reset':
          return this.reset(args, options);
        case 'none':
          return null;
        case 'get-step':
        default:
          return this.getStep(args, options);
      }
    }

    /**
     * Skip form to the next step without adding products to the wizard cart
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
  }, {
    key: "skipStep",
    value: function skipStep() {
      var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      data = this.extendObject({
        action: this.options.ajaxActions.skipStep
      }, data);
      return this.ajaxRequest(data, options);
    }

    /**
     * Submit and skip form to the last step
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
  }, {
    key: "submitAndSkipAll",
    value: function submitAndSkipAll() {
      var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      data = this.extendObject({
        action: this.options.ajaxActions.submitAndSkipAll
      }, data);
      return this.submit(data, options);
    }

    /**
     * Skip form to the last step without adding products to the wizard cart
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
  }, {
    key: "skipAll",
    value: function skipAll() {
      var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      data = this.extendObject({
        action: this.options.ajaxActions.skipAll
      }, data);
      return this.ajaxRequest(data, options);
    }

    /**
     * Get step content by the id
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
  }, {
    key: "getStep",
    value: function getStep() {
      var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      data = this.extendObject({
        action: this.options.ajaxActions.getStep
      }, data);
      return this.ajaxRequest(data, options);
    }

    /**
     * Reset form to the initial state
     * @param {Object} data - object of arguments
     * @param {Object} options - object of method options
     * @returns {Promise} ajax request
     */
  }, {
    key: "reset",
    value: function reset() {
      var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      data = this.extendObject({
        action: this.options.ajaxActions.reset
      }, data);
      return this.ajaxRequest(data, options);
    }
    // </editor-fold>

    // <editor-fold desc="Utils">
    /**
     * Parse any query data to an object
     * https://github.com/cobicarmel/jquery-serialize-object/
     * @param {Object} dataContainer - target
     * @param {Object} key - prop key
     * @param {Object} value - prop value
     * @returns {Object} recursive or null
     */
  }, {
    key: "parseObject",
    value: function parseObject(dataContainer, key, value) {
      var isArrayKey = /^[^\[\]]+\[]/.test(key);
      var isObjectKey = /^[^\[\]]+\[[^\[\]]+]/.test(key);
      var keyName = key.replace(/\[.*/, '');
      if (isArrayKey) {
        if (!dataContainer[keyName]) {
          dataContainer[keyName] = [];
        }
      } else {
        if (!isObjectKey) {
          if (dataContainer.push) {
            dataContainer.push(value);
          } else {
            dataContainer[keyName] = value;
          }
          return null;
        }
        if (!dataContainer[keyName]) {
          dataContainer[keyName] = {};
        }
      }
      var nextKeys = key.match(/\[[^\[\]]*]/g);
      nextKeys[0] = nextKeys[0].replace(/\[|]/g, '');
      return this.parseObject(dataContainer[keyName], nextKeys.join(''), value);
    }

    /**
     * Get FormData as a recursive object
     * https://github.com/cobicarmel/jquery-serialize-object/
     * @param {HTMLFormElement} form - DOM element
     * @returns {Object} form data object
     */
  }, {
    key: "serializeObject",
    value: function serializeObject(form) {
      var formData = new FormData(form);
      var data = {};
      var _iterator7 = _createForOfIteratorHelper(formData.entries()),
        _step7;
      try {
        for (_iterator7.s(); !(_step7 = _iterator7.n()).done;) {
          var pair = _step7.value;
          this.parseObject(data, pair[0], pair[1]);
        }
      } catch (err) {
        _iterator7.e(err);
      } finally {
        _iterator7.f();
      }
      return data;
    }

    /**
     * Get current URL search params
     * @param {String} search - GET string to parse
     * @returns {Object} URLSearchParams
     */
  }, {
    key: "getQueryArgs",
    value: function getQueryArgs() {
      var search = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : this.options.windowNode.location.search;
      if (typeof URLSearchParams === 'undefined') {
        return {};
      }
      return new URLSearchParams(search);
    }

    /**
     * Set URL request parameter value
     * @param {Object} args - key pair of params
     * @returns {this} self instance
     */
  }, {
    key: "setQueryArg",
    value: function setQueryArg(args) {
      if (!this.options.windowNode.history || !this.options.windowNode.history.pushState) {
        return this;
      }
      var queryArgs = this.getQueryArgs();
      if (!queryArgs.get) {
        return this;
      }
      for (var key in args) {
        if (args.hasOwnProperty(key)) {
          if (typeof args[key] === 'boolean' && !args[key]) {
            queryArgs["delete"](key);
          } else {
            queryArgs.set(key, args[key]);
          }
        }
      }
      var path = this.options.windowNode.location.protocol + '//' + this.options.windowNode.location.host + this.options.windowNode.location.pathname + '?' + queryArgs.toString();
      this.options.windowNode.history.pushState({
        path: path
      }, '', path);
      return this;
    }

    /**
     * Parse query string to an object
     * @param {String} string - string to parse
     * @returns {Object} parsed output
     */
  }, {
    key: "queryStringToObject",
    value: function queryStringToObject(string) {
      var output = {};
      if (!string) {
        return output;
      }

      /* eslint-disable */
      var data = JSON.parse('{"' + decodeURI(string).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g, '":"') + '"}');
      /* eslint-enable */

      for (var key in data) {
        if (data.hasOwnProperty(key)) {
          this.parseObject(output, key, data[key]);
        }
      }
      return output;
    }

    /**
     * Send vibration signal
     * @param {Array} args - vibration pattern as duration, pause, duration,..
     * @returns {this} self instance
     */
  }, {
    key: "vibrate",
    value: function vibrate() {
      var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [200];
      if ('vibrate' in this.options.windowNode.navigator) {
        this.options.windowNode.navigator.vibrate(args);
      } else if ('oVibrate' in this.options.windowNode.navigator) {
        this.options.windowNode.navigator.oVibrate(args);
      } else if ('mozVibrate' in this.options.windowNode.navigator) {
        this.options.windowNode.navigator.mozVibrate(args);
      } else if ('webkitVibrate' in this.options.windowNode.navigator) {
        this.options.windowNode.navigator.webkitVibrate(args);
      }
      return this;
    }

    /**
     * Set clipboard content
     * @param {String} text - string to add into clipboard
     * @returns {Boolean} function result
     */
  }, {
    key: "setClipboard",
    value: function setClipboard(text) {
      if (this.options.windowNode.clipboardData && this.options.windowNode.clipboardData.setData) {
        this.options.windowNode.clipboardData.setData('Text', text);
        return true;
      } else if (this.options.documentNode.queryCommandSupported && this.options.documentNode.queryCommandSupported('copy')) {
        var textarea = this.options.documentNode.createElement('textarea');
        textarea.textContent = text;
        textarea.style.position = 'fixed';
        this.options.documentNode.body.appendChild(textarea);
        textarea.select();
        try {
          return this.options.documentNode.execCommand('copy');
        } catch (ex) {
          return false;
        } finally {
          this.options.documentNode.body.removeChild(textarea);
        }
      }
      return false;
    }

    /**
     * Is element on the screen
     * @param {HTMLElement} element - element to check
     * @param {Boolean} strict - check element bottom position also
     * @returns {Boolean} function result
     */
  }, {
    key: "isScrolledIntoView",
    value: function isScrolledIntoView(element) {
      var strict = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
      var rect = element.getBoundingClientRect();
      return !strict && rect.top >= 0 && rect.top <= this.options.windowNode.innerHeight || strict && rect.top >= 0 && rect.bottom <= this.options.windowNode.innerHeight;
    }

    /**
     * Scroll window screen to element
     * @param {HTMLElement} element - scroll to element
     * @param {Number} gap - top space gap
     * @param {String} behavior - animation behavior
     * @returns {this} self instance
     */
  }, {
    key: "scrollToElement",
    value: function scrollToElement(element) {
      var gap = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;
      var behavior = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'smooth';
      var reduceMotion = this.options.windowNode.matchMedia('(prefers-reduced-motion: reduce)') === true || this.options.windowNode.matchMedia('(prefers-reduced-motion: reduce)').matches === true;
      this.options.windowNode.scrollTo({
        top: element.getBoundingClientRect().top - Number(gap),
        behavior: reduceMotion ? 'instant' : behavior
      });
      return this;
    }

    /**
     * Serialize any object
     * https://github.com/knowledgecode/jquery-param
     * @param {Object} input - any object to serialize
     * @returns {String} a serialized string
     */
  }, {
    key: "objectToQueryParam",
    value: function objectToQueryParam(input) {
      /* eslint-disable */
      var output = [];
      var add = function add(key, value) {
        value = typeof value === 'function' ? value() : value;
        value = value === null ? '' : value === undefined ? '' : value;
        output[output.length] = encodeURIComponent(key) + '=' + encodeURIComponent(value);
      };
      var buildParams = function buildParams(prefix, obj) {
        var i, len, key;
        if (prefix) {
          if (Array.isArray(obj)) {
            for (i = 0, len = obj.length; i < len; i++) {
              buildParams(prefix + '[' + (_typeof(obj[i]) === 'object' && obj[i] ? i : '') + ']', obj[i]);
            }
          } else if (Object.prototype.toString.call(obj) === '[object Object]') {
            for (key in obj) {
              buildParams(prefix + '[' + key + ']', obj[key]);
            }
          } else {
            add(prefix, obj);
          }
        } else if (Array.isArray(obj)) {
          for (i = 0, len = obj.length; i < len; i++) {
            add(obj[i].name, obj[i].value);
          }
        } else {
          for (key in obj) {
            buildParams(key, obj[key]);
          }
        }
        return output;
      };
      /* eslint-enable */

      return buildParams('', input).join('&');
    }

    /**
     * Extend object properties by other objects
     * @param {Object} args - object to extend
     * @returns {Object} new extended object
     */
  }, {
    key: "extendObject",
    value: function extendObject() {
      for (var _len = arguments.length, args = new Array(_len), _key3 = 0; _key3 < _len; _key3++) {
        args[_key3] = arguments[_key3];
      }
      return Object.assign.apply(Object, [{}].concat(args));
    }
  }]);
  return WCPW;
}();
export default WCPW;