function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"]; if (!it) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
/* WooCommerce Products Wizard global instance and main event handlers
 * Original author: mail@troll-winner.ru
 * Further changes, comments: mail@troll-winner.ru
 */

(function (root, factory) {
  'use strict';

  if (typeof define === 'function' && define.amd) {
    define(['jquery', 'Masonry'], factory);
  } else if ((typeof exports === "undefined" ? "undefined" : _typeof(exports)) === 'object' && typeof module !== 'undefined' && typeof require === 'function') {
    module.exports = factory(require('jquery'), require('Masonry'));
  } else {
    factory(root.jQuery);
  }
})(this, function ($) {
  'use strict';

  var _this2 = this;
  var wcpw = {
    windowNode: window,
    documentNode: document,
    $window: $(window),
    $document: $(document),
    $body: $(document.body),
    stickyObserverElements: null,
    init: function init() {
      if (typeof $.fn.wcpw === 'undefined') {
        this.windowNode.console.error('jQuery.fn.wcpw is not exist');
        return this;
      }
      this.$document.find('[data-component~="wcpw"]').each(function () {
        var $element = $(this);
        return $element.wcpw($element.data('options') || {});
      });
      return this;
    },
    initVariationForm: function initVariationForm($elements) {
      $elements = $elements || this.$document.find('[data-component~="wcpw-product-variations"]');
      $elements.each(function () {
        var $element = $(this);
        return $element.wcpwVariationForm($element.data('options') || {});
      });
      return this;
    },
    onInit: function onInit(event, instance) {
      // init variation forms
      this.initVariationForm(instance.$element.find('[data-component~="wcpw-product-variations"]'));

      // check product variations form for attached wizard
      if (instance.options && instance.options.attachedProduct) {
        this.$document.find('#' + instance.options.formId).trigger('check_variations');
      }

      // prettyPhoto init
      if (typeof $.fn.prettyPhoto !== 'undefined') {
        instance.$element.find('a[data-rel^="prettyPhoto"]').prettyPhoto({
          hook: 'data-rel',
          social_tools: false,
          theme: 'pp_woocommerce',
          horizontal_padding: 20,
          opacity: 0.8,
          deeplinking: false
        });
      }

      // avada lightbox init
      if (typeof this.windowNode.avadaLightBox !== 'undefined' && typeof this.windowNode.avadaLightBox.activate_lightbox !== 'undefined') {
        this.windowNode.avadaLightBox.activate_lightbox(instance.$element);
      }

      // sticky elements init
      if (typeof $.fn.stick_in_parent !== 'undefined') {
        instance.$element.find('[data-component~="wcpw-sticky"]').each(function () {
          var $element = $(this);
          return $element.stick_in_parent($.extend(
          // older versions support
          {
            parent: $element.data('sticky-parent'),
            offset_top: Number($element.data('sticky-top-offset'))
          }, $element.data('sticky-options')));
        });
      }

      // EPO plugin init
      this.reInitExtraProductOptions(instance);

      // noUi slider init
      this.documentNode.dispatchEvent(new CustomEvent('init.nouislider.wcpw'));

      // init step filter
      this.$document.trigger('init.wcsf');

      // masonry layout init
      if (typeof Masonry !== 'undefined') {
        instance.$element.find('[data-component~="wcpw-masonry"]').each(function () {
          var $element = $(this);
          var masonryInstance = new Masonry(this, $.extend({
            itemSelector: '.col',
            percentPosition: true,
            columnWidth: this.querySelector('[data-component="wcpw-masonry-sizer"]')
          }, $element.data('options')));
          $element.data('masonry-instance', masonryInstance);
        });
      }
    },
    reInitExtraProductOptions: function reInitExtraProductOptions(instance) {
      if (typeof $.tcepo === 'undefined' || typeof $.tcepo.tm_init_epo === 'undefined') {
        return this;
      }
      var _this = this;

      // clear fields cache
      if (typeof $.tc_api_set !== 'undefined') {
        $.tc_api_set('get_element_from_field_cache', []);
      } else if (typeof $.tcAPISet !== 'undefined') {
        $.tcAPISet('getElementFromFieldCache', []);
      }

      // remove old listeners
      this.$document.off('change.cpfurl tmredirect', '.use_url_container .tmcp-select');
      this.$document.off('click.cpfurl change.cpfurl tmredirect', '.use_url_container .tmcp-radio, .use_url_container .tmcp-radio+label');

      // remove old elements
      instance.$element.find('.tmcp-upload-hidden').remove();

      // unique container where the options are embedded. this is usually the parent tag of the cart form
      return instance.$element.find('[data-component~="wcpw-product"]').each(function () {
        var $product = $(this);
        var $options = $product.find('.tc-extra-product-options');
        var productId = $options.attr('data-product-id');
        var epoId = $options.attr('data-epo-id');
        if ($options.length <= 0 || !productId || !epoId) {
          return;
        }
        $.tcepo.tm_init_epo($product, true, productId, epoId);
        _this.$window.trigger('tmlazy');
        if ($.jMaskGlobals) {
          $product.find($.jMaskGlobals.maskElements).each(function () {
            var $element = $(this);
            if ($element.attr('data-mask')) {
              $element.mask($element.attr('data-mask'));
            }
          });
        }
        if ($product.data('type') === 'variable') {
          $product.find('[data-component~="wcpw-product-variations"]').trigger('wc_variation_form.cpf');
        }
      });
    },
    saveExtraProductOptions: function saveExtraProductOptions(productToAdd) {
      if (typeof $.tcepo === 'undefined') {
        return null;
      }
      var productId = productToAdd.product_id;
      var $extraOptions = this.$document.find(".tc-extra-product-options.tm-product-id-".concat(productId));
      if ($extraOptions.length !== 1) {
        return true;
      }
      var $totalsForm = this.$document.find(".tc-totals-form.tm-product-id-".concat(productId));
      var $form = $totalsForm.closest('form');
      var formPrefix = $totalsForm.find('.tc_form_prefix').val();
      var data = {
        tcajax: 1,
        tcaddtocart: productId,
        cpf_product_price: $totalsForm.find('.cpf-product-price').val()
      };
      if (formPrefix) {
        data.tc_form_prefix = formPrefix;
      }
      if ($totalsForm.tc_validate && !$form.tc_validate().form()) {
        return false;
      }
      if ($form.data('wcpw-epo-data') && $form.data('wcpw-epo-data').functions && !$form.data('wcpw-epo-data').functions.apply_submit_events($form.data('wcpw-epo-data').epo)) {
        return false;
      }

      // save collected data into product request arg
      var request = $extraOptions.tm_aserializeObject ? $extraOptions.tm_aserializeObject() : $extraOptions.tcSerializeObject();

      // bug with files upload
      $.each(request, function (key, value) {
        if (Array.isArray(value)) {
          value = value.filter(function (el) {
            return el !== '';
          });
          if (value.length === 0) {
            request[key] = '';
          }
        }
      });
      productToAdd.request = $.extend(request, data);
      return true;
    },
    saveExtraProductOptionsAttachments: function saveExtraProductOptionsAttachments(instance, data) {
      if (typeof $.tcepo === 'undefined') {
        return;
      }
      instance.$element.find('.tc-extra-product-options input[type="file"]').each(function () {
        if (!this.files[0] || !this.files[0].size) {
          return this;
        }
        return data.append(this.name, this.files[0]);
      });
    },
    reInitCheckoutScript: function reInitCheckoutScript() {
      var script = this.documentNode.querySelector('#wc-checkout-js');
      if (!script) {
        return this;
      }
      var src = script.getAttribute('src');
      var id = script.getAttribute('id');
      var clone = this.documentNode.createElement('script');

      // clear all traces
      script.remove();
      this.$body.off('click', 'a.showcoupon');
      this.$body.off('click', '.woocommerce-remove-coupon');
      this.$body.off('click', 'a.showlogin');
      this.$body.off('click', 'a.woocommerce-terms-and-conditions-link');
      clone.src = src;
      clone.id = id;

      // append and init script
      this.documentNode.body.appendChild(clone);

      // init county select
      this.$body.trigger('country_to_state_changed');
      return this;
    },
    updateStickyObserverElementsState: function updateStickyObserverElementsState() {
      if (!this.stickyObserverElements || this.stickyObserverElements.length <= 0) {
        return;
      }
      var _iterator = _createForOfIteratorHelper(this.stickyObserverElements),
        _step;
      try {
        for (_iterator.s(); !(_step = _iterator.n()).done;) {
          var element = _step.value;
          if (!element.parentElement) {
            continue;
          }
          var parentTop = element.parentElement.getBoundingClientRect().top;
          var top = Math.round(element.getBoundingClientRect().top - parentTop);
          element.style.position = 'static';
          element.toggleAttribute('stuck', top !== Math.round(element.getBoundingClientRect().top - parentTop));
          element.style.position = '';
          element.dataset.prevClientTop = String(top);
        }
      } catch (err) {
        _iterator.e(err);
      } finally {
        _iterator.f();
      }
    }
  };
  if (typeof document.wcpw === 'undefined') {
    document.wcpw = wcpw;
  }

  // main actions
  wcpw.$window.on('load', function () {
    // init sticky observer elements
    wcpw.stickyObserverElements = wcpw.documentNode.querySelectorAll('[data-component~="wcpw-sticky-observer"]');
    if (!wcpw.stickyObserverElements || wcpw.stickyObserverElements.length <= 0) {
      return;
    }
    wcpw.updateStickyObserverElementsState();
    wcpw.windowNode.addEventListener('resize', function () {
      return setTimeout(function () {
        return wcpw.updateStickyObserverElementsState();
      }, 0);
    });
    wcpw.windowNode.addEventListener('scroll', function () {
      var _iterator2 = _createForOfIteratorHelper(wcpw.stickyObserverElements),
        _step2;
      try {
        for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
          var element = _step2.value;
          if (!element.parentElement) {
            continue;
          }
          var top = Math.round(element.getBoundingClientRect().top - element.parentElement.getBoundingClientRect().top);
          element.toggleAttribute('stuck', Number(element.dataset.prevClientTop) !== top);
          element.dataset.prevClientTop = String(top);
        }
      } catch (err) {
        _iterator2.e(err);
      } finally {
        _iterator2.f();
      }
    }, false);
  });
  wcpw.$document.ready(function () {
    wcpw.init();

    // contact form 7 hooks
    if (typeof wpcf7 !== 'undefined') {
      wcpw.$document.on('ajaxCompleted.wcpw', function (event, instance) {
        var form = instance.element.querySelector('.wpcf7-form');
        if (form) {
          if (typeof wpcf7.initForm !== 'undefined') {
            wpcf7.initForm($(form));
          } else if (typeof wpcf7.init !== 'undefined') {
            wpcf7.init(form);
          }
        }
      });
    }

    // EPO hooks
    if (typeof $.tcepo !== 'undefined') {
      wcpw.$document.on('submit.wcpw', function (event, instance, data) {
        if (instance && data) {
          // pass data to the request
          $.each(data.productsToAdd, function (key, product) {
            if (typeof data.productsToAddChecked[product.step_id] !== 'undefined' && data.productsToAddChecked[product.step_id].indexOf(product.product_id) !== -1 && !wcpw.saveExtraProductOptions(product)) {
              instance.hasError = true;
              instance.productsWithError.push(product);
            }
          });
        }
      });
      wcpw.$document.on('ajaxRequest.wcpw', function (event, instance, data) {
        // save attachments
        wcpw.saveExtraProductOptionsAttachments(instance, data);
      });
    }
  });
  wcpw.$document.on('init.wcpw', function () {
    return wcpw.init();
  });
  wcpw.$document.on('init.variationForm.wcpw', function () {
    return wcpw.initVariationForm();
  });
  wcpw.$document.on('launched.wcpw', function (event, instance) {
    wcpw.onInit(event, instance);

    // off default WC form scripts
    setTimeout(function () {
      instance.$element.find('[data-component~="wcpw-product-variations"]').off('.wc-variation-form');
    }, 100);
  });

  // ajax actions
  wcpw.$document.on('ajaxCompleted.wcpw', function (event, instance, response) {
    wcpw.onInit(event, instance);

    // re-init sticky observer elements
    wcpw.stickyObserverElements = instance.element.querySelectorAll('[data-component~="wcpw-sticky-observer"]');
    wcpw.updateStickyObserverElementsState();

    // refresh WC mini-cart if wizard reflects the cart
    if (instance.options && instance.options.reflectInMainCart) {
      wcpw.$body.trigger('wc_fragment_refresh');
    }
    if (instance.options && instance.options.enableCheckoutStep && response.finalRedirectUrl && response.preventRedirect) {
      // re-init checkout script
      wcpw.reInitCheckoutScript();
    }
    if (response.hasError) {
      var message = instance.element.querySelector('[data-component~="wcpw-message"]');

      // scroll to the message
      if (message && !instance.isScrolledIntoView(message)) {
        instance.scrollToElement(message, instance.options.scrollingUpGap);
      }

      // vibration signal
      instance.vibrate();
    }
  });
  wcpw.$document.on('submitError.wcpw', function (event, instance) {
    if (!instance || instance.productsWithError.length <= 0) {
      return _this2;
    }
    var $product = instance.$element.find("[data-component~=\"wcpw-product\"][data-id=\"".concat(instance.productsWithError[0].product_id, "\"]") + "[data-step-id=\"".concat(instance.productsWithError[0].step_id, "\"]"));
    if ($product.length <= 0) {
      return _this2;
    }

    // scroll window to the product
    if (!instance.isScrolledIntoView($product.get(0))) {
      instance.scrollToElement($product.get(0), instance.options.scrollingUpGap);
    }
    if (typeof $.fn.modal !== 'undefined') {
      // open product modal with EPO errors
      var $modal = $product.find('[data-component~="wcpw-product-modal"] .tc-extra-product-options');
      if ($modal.length > 0) {
        $modal.closest('[data-component~="wcpw-product-modal"]').modal('show');
      }
    }

    // vibration signal
    instance.vibrate();
    return _this2;
  });
  wcpw.$document.on('ajaxRequest.wcpw', function (event, instance) {
    if (typeof $.fn.modal !== 'undefined') {
      // close products modals
      instance.$element.find('[data-component~="wcpw-product-modal"].show').modal('hide');
    }
  });

  // toggle element
  wcpw.$document.on('toggle.wcpw', function () {
    return wcpw.$body.trigger('sticky_kit:recalc');
  });

  // disable/enable add-to-cart button for attached wizards
  wcpw.$document.on('hide_variation', '.variations_form', function () {
    var $form = $(this);
    var $addToCartBtn = $form.closest('.product').find("[data-component~=\"wcpw-add-to-cart\"][form=\"".concat($form.attr('id'), "\"]"));
    if ($addToCartBtn.length > 0) {
      $addToCartBtn.addClass('disabled').attr('disabled', true);
    }
  });
  wcpw.$document.on('show_variation', '.variations_form', function () {
    var $form = $(this);
    var $addToCartBtn = $form.closest('.product').find("[data-component~=\"wcpw-add-to-cart\"][form=\"".concat($form.attr('id'), "\"]"));
    if ($addToCartBtn.length > 0) {
      $addToCartBtn.removeClass('disabled').removeAttr('disabled');
    }
  });

  // save epo data for further validations
  wcpw.$window.on('tm-from-submit', function (event, data) {
    if (data.epo) {
      $(data.epo.form.eq(0)).data('wcpw-epo-data', data);
    }
  });

  // support deprecated actions
  // @since 9.2.0
  var actions = {
    'launched.wcProductsWizard': 'launched.wcpw',
    'ajaxRequest.wcProductsWizard': 'ajaxRequest.wcpw',
    'ajaxCompleted.wcProductsWizard': 'ajaxCompleted.wcpw',
    'addToMainCart.wcProductsWizard': 'addToMainCart.wcpw',
    'addToMainCart.error.wcProductsWizard': 'addToMainCartError.wcpw',
    'addToMainCart.error.wcpw': 'addToMainCartError.wcpw',
    'addToMainCartRedirect.wcProductsWizard': 'addToMainCartRedirect.wcpw',
    'submit.wcProductsWizard': 'submit.wcpw',
    'submit.error.wcProductsWizard': 'submitError.wcpw',
    'submit.error.wcpw': 'submitError.wcpw'
  };
  $.each(actions, function (oldAction, newAction) {
    wcpw.$document.on(newAction, function (event) {
      for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
        args[_key - 1] = arguments[_key];
      }
      wcpw.$document.trigger(oldAction, args);
    });
  });
});