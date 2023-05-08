function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
/* WooCommerce Products Wizard main event handlers
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

  var $document = $(document);
  var $body = $(document.body);
  function toggleStyleSettingFields($element) {
    $element.closest('table').find('tr:has([data-mode])').addClass('hidden').filter(":has([data-mode=\"".concat($element.val(), "\"])")).removeClass('hidden');
  }
  function toggleAvailabilityRulesFields($element) {
    $element.find('[data-component~="wcpw-data-table-body-item"][data-key="source"] :input').each(function () {
      var $input = $(this);
      $input.closest('[data-component~="wcpw-data-table-body-item"]').attr('data-value', $input.val());
    });
  }
  function setQueryArg(args) {
    if (!window.history || !window.history.pushState) {
      return this;
    }
    var params = new URLSearchParams(window.location.search);
    for (var key in args) {
      if (args.hasOwnProperty(key)) {
        params.set(key, args[key]);
      }
    }
    var path = window.location.protocol + '//' + window.location.host + window.location.pathname + '?' + params.toString();
    window.history.pushState({
      path: path
    }, '', path);
    return this;
  }
  function initScripts() {
    $body.trigger('wc-enhanced-select-init');
    $document.trigger('init.thumbnail.wcpw');
    $document.trigger('init.ajaxSelect.wcpw');
    $document.trigger('init.multiSelect.wcpw');
    $document.trigger('init.dataTable.wcpw');
  }
  $document.ready(function () {
    toggleAvailabilityRulesFields($document);
    toggleStyleSettingFields($('#woocommerce_products_wizard_custom_styles_mode'));
  });

  // get step setting
  $document.on('get.settings.item.steps.wcpw', function (event, instance) {
    initScripts();
    toggleAvailabilityRulesFields(instance.$modalBody);
  });

  // data table item added
  $document.on('added.item.dataTable.wcpw', function (event, instance, $element) {
    initScripts();
    toggleAvailabilityRulesFields($element);
  });

  // data table item cloned
  $document.on('cloned.item.dataTable.wcpw', function (event, instance, $element) {
    initScripts();
    toggleAvailabilityRulesFields($element);
  });

  // product variations are loaded
  $document.on('woocommerce_variations_loaded', '#woocommerce-product-data', function () {
    initScripts();
    toggleAvailabilityRulesFields($(this));
  });

  // availability setting source change
  $document.on('change', '[data-component~="wcpw-data-table-body-item"][data-key="source"] :input', function () {
    var $input = $(this);
    $input.closest('[data-component~="wcpw-data-table-body-item"]').attr('data-value', $input.val());
  });

  // set default cart content
  $document.on('click', '[data-component~="wcpw-set-default-cart-content"]', function (event) {
    event.preventDefault();
    if (!confirm(this.getAttribute('data-confirm-message'))) {
      return this;
    }
    var data = {
      action: 'wcpwSetDefaultCartContentAjax',
      id: this.getAttribute('data-id')
    };
    if (typeof this.getAttribute('data-step-id') !== 'undefined') {
      data.stepId = this.getAttribute('data-step-id');
    }
    if (typeof this.getAttribute('data-value') !== 'undefined') {
      data.value = this.getAttribute('data-value');
    }
    return $.post(this.getAttribute('data-ajax-url'), data, function (response) {
      if (!response) {
        return;
      }
      if (typeof response.count !== 'undefined') {
        document.querySelector('[data-component~="wcpw-default-cart-content-count"]').innerHTML = response.count;
      }
      if (response.message) {
        alert(response.message);
      }
    }, 'json');
  });

  // toggle settings group
  $document.on('click', '[data-component~="wcpw-settings-group-toggle"]', function (event) {
    event.preventDefault();
    var $element = $(this);
    var $groups = $element.closest('[data-component~="wcpw-settings-groups"]');
    var $content = $groups.find('[data-component~="wcpw-settings-group-content"]');
    var $toggle = $groups.find('[data-component~="wcpw-settings-group-toggle"]');
    var $selectedContent = $content.filter("[data-id=\"".concat($element.data('id'), "\"]"));
    var isClosed = $selectedContent.attr('aria-expanded') === 'false';
    if (!isClosed) {
      $element.add($selectedContent).attr('aria-expanded', 'false');
      return this;
    }
    $toggle.add($content).attr('aria-expanded', 'false');
    $element.add($selectedContent).attr('aria-expanded', 'true');

    // save state to URL parameter
    setQueryArg({
      activeGroup: $element.data('id')
    });
    return this;
  });

  // bulk action handle
  $document.on('click', '#doaction, #doaction2', function () {
    var $element = $(this);
    var $select = $element.prev('select');
    if ($select.val() === 'edit') {
      setTimeout(function () {
        $('#woocommerce-fields-bulk').append($('#wcpw-bulk-edit-fields-template').html());

        // re-init libraries
        $body.trigger('wc-enhanced-select-init');
        $document.trigger('init.dataTable.wcpw');
        toggleAvailabilityRulesFields($document);
      }, 0);
    }
  });

  // on edit cancel fix
  $document.on('mouseover focus', '#doaction, #doaction2', function () {
    if (!$('#bulk-edit').is(':visible')) {
      $('#wcpw-bulk-edit-fields').remove();
    }
  });

  // custom styles mode change
  $document.on('change', '#woocommerce_products_wizard_custom_styles_mode', function () {
    toggleStyleSettingFields($(this));
  });

  // settings reset click
  $document.on('click', '[data-component~="wcpw-settings-reset"]', function (event) {
    if (!confirm(this.getAttribute('data-confirm-message'))) {
      event.preventDefault();
    }
  });

  // thumbnail generator area added event
  $document.on('added.area.thumbnailGenerator.wcpw cloned.area.thumbnailGenerator.wcpw', function () {
    initScripts();
  });

  // thumbnail image selected
  $document.on('selected.thumbnail.wcpw', function (event, instance, attachment) {
    // append image into thumbnail generator
    if (instance.$element.data('component') === 'wcpw-thumbnail-generator-area-image wcpw-thumbnail') {
      var attachmentJson = attachment.toJSON();
      if (!attachmentJson.id) {
        return null;
      }
      var $area = instance.$element.closest('[data-component~="wcpw-thumbnail-generator-area"]').find('[data-component~="wcpw-thumbnail-generator-area-inner"]');
      $area.children('img').remove();
      $area.append("<img src=\"".concat(attachmentJson.url, "\">"));
    }
    return this;
  });

  // thumbnail image removed
  $document.on('removed.thumbnail.wcpw', function (event, instance) {
    // remove image from thumbnail generator
    if (instance.$element.data('component') === 'wcpw-thumbnail-generator-area-image wcpw-thumbnail') {
      instance.$element.closest('[data-component~="wcpw-thumbnail-generator-area"]').find('[data-component~="wcpw-thumbnail-generator-area-inner"] > img').remove();
    }
  });

  // templates item folder checkbox click
  $document.on('change', '[data-component~="wcpw-templates-list-item-input"][data-type="folder"]', function () {
    var $element = $(this);
    $element.closest('[data-component~="wcpw-templates-list-item"]').find('[data-component~="wcpw-templates-list-item-input"]').prop('checked', $element.prop('checked'));
  });

  // select2 clear value fix
  $('.wc-product-search[data-allow_clear="1"][data-multiple="false"]').on('select2:unselect', function () {
    $(this).html('<option value=""></option>');
  });
});