function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"]; if (!it) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }
/* WooCommerce Products Wizard global instance and main event handlers
 * Original author: mail@troll-winner.ru
 * Further changes, comments: mail@troll-winner.ru
 */

import { WCPW } from 'wcpw';
import { WCPWVariationForm } from 'wcpw-variation-form';
import { Masonry } from 'masonry-layout';
import { jQuery as $ } from 'jquery';
var wcpw = {
  windowNode: window,
  documentNode: document,
  $window: typeof $ !== 'undefined' ? $(window) : null,
  $document: typeof $ !== 'undefined' ? $(document) : null,
  $body: typeof $ !== 'undefined' ? $(document.body) : null,
  stickyObserverElements: null,
  init: function init() {
    if (typeof WCPW === 'undefined') {
      this.windowNode.console.error('WCPW class is not exist');
      return this;
    }
    var _iterator = _createForOfIteratorHelper(this.documentNode.querySelectorAll('[data-component~="wcpw"]')),
      _step;
    try {
      for (_iterator.s(); !(_step = _iterator.n()).done;) {
        var element = _step.value;
        element.wcpw = new WCPW(element, JSON.parse(element.getAttribute('data-options')) || {});
        element.wcpw.init();
      }
    } catch (err) {
      _iterator.e(err);
    } finally {
      _iterator.f();
    }
    return this;
  },
  initVariationForm: function initVariationForm(elements) {
    if (typeof WCPWVariationForm === 'undefined') {
      this.windowNode.console.error('WCPWVariationForm class is not exist');
      return this;
    }
    elements = elements || this.documentNode.querySelectorAll('[data-component~="wcpw-product-variations"]');
    var _iterator2 = _createForOfIteratorHelper(elements),
      _step2;
    try {
      for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
        var element = _step2.value;
        element.wcpwVariationForm = new WCPWVariationForm(element, JSON.parse(element.getAttribute('data-options')) || {});
        element.wcpwVariationForm.init();
      }
    } catch (err) {
      _iterator2.e(err);
    } finally {
      _iterator2.f();
    }
    return this;
  },
  onInit: function onInit(event) {
    var instance = event.detail.instance;

    // init variation forms
    this.initVariationForm(instance.element.querySelectorAll('[data-component~="wcpw-product-variations"]'));

    // check product variations form for attached wizard
    if (this.$document && instance.options && instance.options.attachedProduct) {
      this.$document.find('#' + instance.options.formId).trigger('check_variations');
    }

    // prettyPhoto init
    if (typeof $ !== 'undefined' && typeof $.fn.prettyPhoto !== 'undefined') {
      $(instance.element).find('a[data-rel^="prettyPhoto"]').prettyPhoto({
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
      this.windowNode.avadaLightBox.activate_lightbox($(instance.element));
    }

    // sticky elements init
    if (typeof $ !== 'undefined' && typeof $.fn.stick_in_parent !== 'undefined') {
      var _iterator3 = _createForOfIteratorHelper(instance.element.querySelectorAll('[data-component~="wcpw-sticky"]')),
        _step3;
      try {
        for (_iterator3.s(); !(_step3 = _iterator3.n()).done;) {
          var element = _step3.value;
          $(element).stick_in_parent(Object.assign(
          // older versions support
          {
            parent: element.getAttribute('data-sticky-parent'),
            offset_top: Number(element.getAttribute('data-sticky-top-offset'))
          }, JSON.parse(element.getAttribute('data-sticky-options') || '{}')));
        }
      } catch (err) {
        _iterator3.e(err);
      } finally {
        _iterator3.f();
      }
    }

    // EPO plugin init
    this.reInitExtraProductOptions(instance);

    // noUi slider init
    this.documentNode.dispatchEvent(new CustomEvent('init.nouislider.wcpw'));

    // init step filter
    this.documentNode.dispatchEvent(new CustomEvent('init.wcsf'));

    // masonry layout init
    if (typeof Masonry !== 'undefined') {
      var _iterator4 = _createForOfIteratorHelper(instance.element.querySelectorAll('[data-component~="wcpw-masonry"]')),
        _step4;
      try {
        for (_iterator4.s(); !(_step4 = _iterator4.n()).done;) {
          var _element = _step4.value;
          _element.masonryInstance = new Masonry(_element, Object.assign({
            itemSelector: '.col',
            percentPosition: true,
            columnWidth: _element.querySelector('[data-component="wcpw-masonry-sizer"]')
          }, JSON.parse(_element.getAttribute('data-options') || '{}')));
        }
      } catch (err) {
        _iterator4.e(err);
      } finally {
        _iterator4.f();
      }
    }
  },
  reInitExtraProductOptions: function reInitExtraProductOptions(instance) {
    if (typeof $ === 'undefined' || typeof $.tcepo === 'undefined' || typeof $.tcepo.tm_init_epo === 'undefined') {
      return;
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
    instance.element.querySelectorAll('.tmcp-upload-hidden').remove();

    // unique container where the options are embedded. this is usually the parent tag of the cart form
    var _iterator5 = _createForOfIteratorHelper(instance.element.querySelectorAll('[data-component~="wcpw-product"]')),
      _step5;
    try {
      for (_iterator5.s(); !(_step5 = _iterator5.n()).done;) {
        var product = _step5.value;
        var $product = $(product);
        var options = product.querySelector('.tc-extra-product-options');
        var productId = options.getAttribute('data-product-id');
        var epoId = options.getAttribute('data-epo-id');
        if (!options || !productId || !epoId) {
          continue;
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
        if (product.getAttribute('data-type') === 'variable') {
          $product.find('[data-component~="wcpw-product-variations"]').trigger('wc_variation_form.cpf');
        }
      }
    } catch (err) {
      _iterator5.e(err);
    } finally {
      _iterator5.f();
    }
  },
  saveExtraProductOptions: function saveExtraProductOptions(productToAdd) {
    if (typeof $ === 'undefined' || typeof $.tcepo === 'undefined') {
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
    var _iterator6 = _createForOfIteratorHelper(request),
      _step6;
    try {
      for (_iterator6.s(); !(_step6 = _iterator6.n()).done;) {
        var key = _step6.value;
        var value = request[key];
        if (Array.isArray(value)) {
          value = value.filter(function (el) {
            return el !== '';
          });
          if (value.length === 0) {
            request[key] = '';
          }
        }
      }
    } catch (err) {
      _iterator6.e(err);
    } finally {
      _iterator6.f();
    }
    productToAdd.request = Object.assign(request, data);
    return true;
  },
  saveExtraProductOptionsAttachments: function saveExtraProductOptionsAttachments(instance, data) {
    if (typeof $.tcepo === 'undefined') {
      return;
    }
    var _iterator7 = _createForOfIteratorHelper(instance.element.querySelectorAll('.tc-extra-product-options input[type="file"]')),
      _step7;
    try {
      for (_iterator7.s(); !(_step7 = _iterator7.n()).done;) {
        var element = _step7.value;
        if (element.files[0] && element.files[0].size) {
          data.append(element.name, element.files[0]);
        }
      }
    } catch (err) {
      _iterator7.e(err);
    } finally {
      _iterator7.f();
    }
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
    if (this.$body) {
      this.$body.off('click', 'a.showcoupon');
      this.$body.off('click', '.woocommerce-remove-coupon');
      this.$body.off('click', 'a.showlogin');
      this.$body.off('click', 'a.woocommerce-terms-and-conditions-link');
    }
    clone.src = src;
    clone.id = id;

    // append and init script
    this.documentNode.body.appendChild(clone);

    // init county select
    if (this.$body) {
      this.$body.trigger('country_to_state_changed');
    }
    return this;
  },
  updateStickyObserverElementsState: function updateStickyObserverElementsState() {
    if (!this.stickyObserverElements || this.stickyObserverElements.length <= 0) {
      return;
    }
    var _iterator8 = _createForOfIteratorHelper(this.stickyObserverElements),
      _step8;
    try {
      for (_iterator8.s(); !(_step8 = _iterator8.n()).done;) {
        var element = _step8.value;
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
      _iterator8.e(err);
    } finally {
      _iterator8.f();
    }
  }
};

// main actions
wcpw.windowNode.addEventListener('load', function () {
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
    var _iterator9 = _createForOfIteratorHelper(wcpw.stickyObserverElements),
      _step9;
    try {
      for (_iterator9.s(); !(_step9 = _iterator9.n()).done;) {
        var element = _step9.value;
        if (!element.parentElement) {
          continue;
        }
        var top = Math.round(element.getBoundingClientRect().top - element.parentElement.getBoundingClientRect().top);
        element.toggleAttribute('stuck', Number(element.dataset.prevClientTop) !== top);
        element.dataset.prevClientTop = String(top);
      }
    } catch (err) {
      _iterator9.e(err);
    } finally {
      _iterator9.f();
    }
  }, false);
});
wcpw.documentNode.addEventListener('DOMContentLoaded', function () {
  wcpw.init();

  // contact form 7 hooks
  if (typeof wpcf7 !== 'undefined') {
    wcpw.documentNode.addEventListener('ajaxCompleted.wcpw', function (event) {
      var form = event.detail.instance.element.querySelector('.wpcf7-form');
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
  if (typeof $ !== 'undefined' && typeof $.tcepo !== 'undefined') {
    wcpw.documentNode.addEventListener('submit.wcpw', function (event) {
      var instance = event.detail.instance;
      var data = event.detail.data;
      if (instance && data) {
        // pass data to the request
        var _iterator10 = _createForOfIteratorHelper(data.productsToAdd),
          _step10;
        try {
          for (_iterator10.s(); !(_step10 = _iterator10.n()).done;) {
            var key = _step10.value;
            var product = data.productsToAdd[key];
            if (typeof data.productsToAddChecked[product.step_id] !== 'undefined' && data.productsToAddChecked[product.step_id].indexOf(product.product_id) !== -1 && !wcpw.saveExtraProductOptions(product)) {
              instance.hasError = true;
              instance.productsWithError.push(product);
            }
          }
        } catch (err) {
          _iterator10.e(err);
        } finally {
          _iterator10.f();
        }
      }
    });
    wcpw.documentNode.addEventListener('ajaxRequest.wcpw', function (event) {
      // save attachments
      wcpw.saveExtraProductOptionsAttachments(event.detail.instance, event.detail.data);
    });
  }
});
wcpw.documentNode.addEventListener('init.wcpw', function () {
  return wcpw.init();
});
wcpw.documentNode.addEventListener('launched.wcpw', function (event) {
  var instance = event.detail.instance;
  wcpw.onInit(event);
  if (typeof $ !== 'undefined') {
    // off default WC form scripts
    setTimeout(function () {
      $(instance.element).find('[data-component~="wcpw-product-variations"]').off('.wc-variation-form');
    }, 100);
  }
});

// ajax actions
wcpw.documentNode.addEventListener('ajaxCompleted.wcpw', function (event) {
  var instance = event.detail.instance;
  var response = event.detail.response;
  wcpw.onInit(event);

  // re-init sticky observer elements
  wcpw.stickyObserverElements = instance.element.querySelectorAll('[data-component~="wcpw-sticky-observer"]');
  wcpw.updateStickyObserverElementsState();

  // refresh WC mini-cart if wizard reflects the cart
  if (wcpw.$body && instance.options && instance.options.reflectInMainCart) {
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
wcpw.documentNode.addEventListener('submitError.wcpw', function (event) {
  var instance = event.detail.instance;
  if (!instance || instance.productsWithError.length <= 0) {
    return;
  }
  var product = instance.element.querySelector("[data-component~=\"wcpw-product\"][data-id=\"".concat(instance.productsWithError[0].product_id, "\"]") + "[data-step-id=\"".concat(instance.productsWithError[0].step_id, "\"]"));
  if (!product) {
    return;
  }

  // scroll window to the product
  if (!instance.isScrolledIntoView(product)) {
    instance.scrollToElement(product, instance.options.scrollingUpGap);
  }
  if (typeof $.fn.modal !== 'undefined') {
    // open product modal with EPO errors
    var $modal = $(product).find('[data-component~="wcpw-product-modal"] .tc-extra-product-options');
    if ($modal.length > 0) {
      $modal.closest('[data-component~="wcpw-product-modal"]').modal('show');
    }
  }

  // vibration signal
  instance.vibrate();
});
wcpw.documentNode.addEventListener('ajaxRequest.wcpw', function (event) {
  var instance = event.detail.instance;
  if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
    // close products modals
    $(instance.element).find('[data-component~="wcpw-product-modal"].show').modal('hide');
  }
});

// toggle element
wcpw.documentNode.addEventListener('toggle.wcpw', function () {
  if (wcpw.$body) {
    wcpw.$body.trigger('sticky_kit:recalc');
  }
});
if (wcpw.$document) {
  // disable/enable add-to-cart button for attached wizards
  wcpw.$document.on('hide_variation', '.variations_form', function () {
    var addToCartBtn = this.closest('.product').querySelector("[data-component~=\"wcpw-add-to-cart\"][form=\"".concat(this.getAttribute('id'), "\"]"));
    if (addToCartBtn.length > 0) {
      addToCartBtn.classList.add('disabled');
      addToCartBtn.setAttribute('disabled', 'disabled');
    }
  });
  wcpw.$document.on('show_variation', '.variations_form', function () {
    var addToCartBtn = this.closest('.product').querySelector("[data-component~=\"wcpw-add-to-cart\"][form=\"".concat(this.getAttribute('id'), "\"]"));
    if (addToCartBtn.length > 0) {
      addToCartBtn.classList.remove('disabled');
      addToCartBtn.removeAttribute('disabled');
    }
  });
}
if (wcpw.$window) {
  // save epo data for further validations
  wcpw.$window.on('tm-from-submit', function (event, data) {
    if (data.epo) {
      $(data.epo.form.eq(0)).data('wcpw-epo-data', data);
    }
  });
}
export default wcpw;