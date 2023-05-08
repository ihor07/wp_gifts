function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
/* WooCommerce Products Wizard Steps
 * Original author: Alex Troll
 * Further changes, comments: mail@troll-winner.com
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

  var pluginName = 'wcpwSteps';
  var defaults = {};
  var $document = $(document);
  var Plugin = function Plugin(element, options) {
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
    this.$list = this.$element.find('[data-component~="wcpw-steps-list"]');
    this.$modal = $('[data-component~="wcpw-step-modal"]');
    this.$modalBody = this.$modal.find('[data-component~="wcpw-step-modal-body"]');
    this.ajaxUrl = this.element.getAttribute('data-ajax-url');
    if ($.fn.sortable) {
      // init sortable
      this.$list.sortable({
        items: '[data-component~="wcpw-steps-list-item"]',
        distance: 50
      });
    }
    return this;
  };

  /**
   * Add the new element
   * @returns {Object} jQuery new element
   */
  Plugin.prototype.addItem = function () {
    var $listChildren = this.$list.children();
    var id = 0;
    if ($listChildren.length) {
      $listChildren.each(function () {
        id = Math.max(id, Number(this.getAttribute('data-id')));
      });
    }
    id++;
    var $newItem = this.$element.find('[data-component~="wcpw-steps-list-item-template"]').clone().appendTo(this.$list).attr('data-component', 'wcpw-steps-list-item').attr('data-id', id);
    var $newItemSettings = $newItem.find('[data-component~="wcpw-steps-list-item-settings"]');
    var $newItemClone = $newItem.find('[data-component~="wcpw-steps-list-item-clone"]');
    $newItem.find('[data-component~="wcpw-steps-list-item-name"]').text("#".concat(id)).end().find('[data-component~="wcpw-steps-list-item-id"]').attr('name', "_steps_ids[".concat(id, "]")).attr('value', id);
    $newItemSettings.attr('data-settings', $newItemSettings.attr('data-settings').replace(/%STEP_ID%/g, id));
    $newItemClone.attr('data-settings', $newItemClone.attr('data-settings').replace(/%STEP_ID%/g, id));
    this.$element.trigger('added.item.steps.wcpw', [this, $newItemClone]);
    return $newItem;
  };

  /**
   * Show modal
   * @returns {this} self instance
   */
  Plugin.prototype.showModal = function () {
    this.$modal.addClass('is-opened');
    return this;
  };

  /**
   * Hide modal
   * @returns {this} self instance
   */
  Plugin.prototype.hideModal = function () {
    this.$modal.removeClass('is-opened');
    return this;
  };

  /**
   * Get step settings form
   * @param {Object} args - data
   * @returns {Promise} request
   */
  Plugin.prototype.getSettings = function (args) {
    var _this = this;
    var data = $.extend({}, {
      action: 'wcpwGetStepSettingsForm'
    }, args);
    return $.get(this.ajaxUrl, data, function (response) {
      // append data
      _this.$modalBody.html(response);

      // filter label change
      _this.$modalBody.find('[data-component~="wcpw-data-table"][data-key="filters"]' + ' [data-component="wcpw-data-table-body-item"][data-key="label"] input').each(function () {
        if (!this.value) {
          return;
        }
        var $element = $(this);
        $element.closest('[data-component="wcpw-data-table-item"]').find('[data-component="wcpw-data-table-item-open-modal"]').attr('data-name', $element.val());
      });

      // show the modal
      _this.showModal();
      _this.$element.trigger('get.settings.item.steps.wcpw', [_this, response]);
    });
  };

  /**
   * Save step settings
   * @param {Object} $form - jQuery element
   * @returns {this} self instance
   */
  Plugin.prototype.saveSettings = function ($form) {
    var title = $form.find('#title').val();
    var notes = $form.find('#notes').val();
    var stepId = $form.attr('data-step-id');
    var data = {
      action: 'wcpwSaveStepSettings',
      post_id: $form.attr('data-post-id'),
      step_id: stepId,
      values: $form.serialize()
    };
    this.$list.find("[data-component=\"wcpw-steps-list-item\"][data-id=\"".concat(stepId, "\"] ") + "[data-component=\"wcpw-steps-list-item-name\"]").html("#".concat(stepId, " ").concat(title) + (notes ? " <small>(".concat(notes, ")</small>") : ''));
    return $.post(this.ajaxUrl, data, null, 'json');
  };

  /**
   * Clone step with settings
   * @param {Number} id - wizard id
   * @param {Number} sourceStep - source step id
   * @param {Number} targetStep - target step id
   * @returns {this} self instance
   */
  Plugin.prototype.cloneItem = function (id, sourceStep, targetStep) {
    var data = {
      action: 'wcpwCloneStepSettings',
      post_id: id,
      source_step: sourceStep,
      target_step: targetStep
    };
    return $.post(this.ajaxUrl, data, null, 'json');
  };

  /**
   * Remove element
   * @param {Object} $item - jQuery element
   * @returns {this} self instance
   */
  Plugin.prototype.removeItem = function ($item) {
    $item.remove();
    this.$element.trigger('removed.item.steps.wcpw', [this]);
    return this;
  };

  /**
   * Check HTML form validity
   * @param {Object} $form - jQuery object
   * @returns {Boolean} is valid
   */
  Plugin.prototype.checkFromValidity = function ($form) {
    var isValid = true;
    if ($form.length === 0) {
      return isValid;
    }
    $form.each(function () {
      if (this.checkValidity !== 'undefined') {
        isValid = this.checkValidity();
      }
    });
    return isValid;
  };
  $.fn[pluginName] = function (options) {
    return this.each(function () {
      if (!$.data(this, pluginName)) {
        $.data(this, pluginName, new Plugin(this, options));
      }
    });
  };
  var init = function init() {
    return $('[data-component~="wcpw-steps"]').each(function () {
      return $(this).wcpwSteps();
    });
  };
  $document.ready(function () {
    return init();
  });
  $document.on('init.steps.wcpw', function () {
    return init();
  });

  // add form item
  $document.on('click', '[data-component~="wcpw-steps-add"]', function (event) {
    event.preventDefault();
    var $button = $(this);
    var $wcpwSteps = $button.closest('[data-component~="wcpw-steps"]');
    if ($wcpwSteps.data(pluginName)) {
      return $wcpwSteps.data(pluginName).addItem();
    }
    return this;
  });

  // remove form item
  $document.on('click', '[data-component~="wcpw-steps-list-item-remove"]', function (event) {
    event.preventDefault();
    var $button = $(this);
    var $wcpwSteps = $button.closest('[data-component~="wcpw-steps"]');
    if ($wcpwSteps.data(pluginName)) {
      return $wcpwSteps.data(pluginName).removeItem($button.closest('[data-component~="wcpw-steps-list-item"]'));
    }
    return this;
  });

  // open settings modal
  $document.on('click', '[data-component~="wcpw-steps-list-item-settings"]', function (event) {
    event.preventDefault();
    var $button = $(this);
    var $wcpwSteps = $button.closest('[data-component~="wcpw-steps"]');
    $button.addClass('is-loading');
    if ($wcpwSteps.data(pluginName)) {
      return $wcpwSteps.data(pluginName).getSettings($button.data('settings')).always(function () {
        return $button.removeClass('is-loading');
      });
    }
    return this;
  });

  // clone step
  $document.on('click', '[data-component~="wcpw-steps-list-item-clone"]', function (event) {
    event.preventDefault();
    var $button = $(this);
    var $wcpwSteps = $button.closest('[data-component~="wcpw-steps"]');
    $button.addClass('is-loading');
    if ($wcpwSteps.data(pluginName)) {
      var $newItem = $wcpwSteps.data(pluginName).addItem();
      return $wcpwSteps.data(pluginName).cloneItem($button.data('settings').post_id, $button.data('settings').step_id, $newItem.data('id')).always(function () {
        return $button.removeClass('is-loading');
      });
    }
    return this;
  });

  // save the item settings
  $document.on('submit', '[data-component~="wcpw-step-settings-form"]', function (event) {
    var $form = $(this);
    var $wcpwSteps = $('[data-component~="wcpw-steps"]');
    if ($wcpwSteps.data(pluginName)) {
      if (!$wcpwSteps.data(pluginName).checkFromValidity($form)) {
        $form.find('[data-component~="wcpw-settings-group-content"]').attr('aria-expanded', 'true');
        return this;
      }
      event.preventDefault();
      $wcpwSteps.data(pluginName).hideModal().saveSettings($form);
    }
    return this;
  });

  // close modal
  $document.on('click', '[data-component~="wcpw-step-modal-close"]', function (event) {
    event.preventDefault();
    var $wcpwSteps = $('[data-component~="wcpw-steps"]');
    if ($wcpwSteps.data(pluginName)) {
      $wcpwSteps.data(pluginName).hideModal();
    }
    return this;
  });

  // change item template setting
  $document.on('change', '[data-component~="wcpw-step-settings-form"] #item_template', function () {
    var $itemTemplate = $(this);
    var $itemTemplatePreview = $itemTemplate.next('[data-component="wcpw-form-item-template-preview"]');
    $itemTemplatePreview.html("<img alt=\"\" src=\"".concat($itemTemplatePreview.data('src')).concat($itemTemplate.val(), ".png\">"));
  });

  // apply to all steps checkbox click
  $document.on('change', '[data-component~="wcpw-step-setting-apply-to-all-input"]', function () {
    var $element = $(this);
    $element.closest('[data-component~="wcpw-step-setting-apply-to-all-label"]')[$element.is(':checked') ? 'addClass' : 'removeClass']('is-active');
  });

  // filter label change
  $document.on('change', '[data-component~="wcpw-data-table"][data-key="filters"]' + ' [data-component="wcpw-data-table-body-item"][data-key="label"] input', function () {
    if (!this.value) {
      return;
    }
    var $element = $(this);
    $element.closest('[data-component="wcpw-data-table-item"]').find('[data-component="wcpw-data-table-item-open-modal"]').attr('data-name', $element.val());
  });
});