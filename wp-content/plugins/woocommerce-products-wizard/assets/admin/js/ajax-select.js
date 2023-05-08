function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
/* WooCommerce Products Wizard Ajax Select
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

  var pluginName = 'wcpwAjaxSelect';
  var defaults = {};
  var ajaxRequests = {};
  var ajaxRequestsCache = {};
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
    var _this = this;
    this.$element = $(this.element);
    this.$input = this.$element.next('[data-component~="wcpw-ajax-select-input"]');
    this.$target = false;
    this.ajaxUrl = this.$element.data('ajax-url');
    if (this.$element.data('target-parent')) {
      this.$target = this.$element.parents(this.$element.data('target-parent'));
    }
    if (this.$element.data('target-selector')) {
      if (this.$target) {
        this.$target = this.$target.find(this.$element.data('target-selector'));
      } else {
        this.$target = $(this.$element.data('target-selector'));
      }
    }

    // set items by default
    this.getItems().done(function (response) {
      _this.updateItems(response);
      if (_this.$element.data('value')) {
        _this.$element.val(_this.$element.data('value'));
      }
    });
    this.$target.off('change.ajaxSelect.wcpw').on('change.ajaxSelect.wcpw', function () {
      return _this.getItems().done(function (response) {
        return _this.updateItems(response);
      });
    });
    return this;
  };

  /**
   * Get items list via ajax
   * @returns {Promise} request
   */
  Plugin.prototype.getItems = function () {
    var _this2 = this;
    var action = this.$element.data('action');
    var value = this.$target.val();
    if (value === '' || typeof value === 'undefined') {
      return $.when('');
    }
    var data = {
      action: action,
      value: value
    };
    if (ajaxRequestsCache[action] && ajaxRequestsCache[action][value]) {
      return $.when(ajaxRequestsCache[action][value]);
    }
    if (ajaxRequests[action] && ajaxRequests[action][value]) {
      return ajaxRequests[action][value];
    }
    var request = $.ajax({
      url: this.ajaxUrl,
      data: data,
      dataType: 'json',
      success: function success(response) {
        if (!ajaxRequestsCache[action]) {
          ajaxRequestsCache[action] = {};
        }
        ajaxRequestsCache[action][value] = response;
        _this2.$element.trigger('updated.items.ajaxSelect.wcpw', [_this2, response]);
      }
    });
    if (!ajaxRequests[action]) {
      ajaxRequests[action] = {};
    }
    ajaxRequests[action][value] = request;
    return request;
  };

  /**
   * Update items list
   * @param {Object} data - args to pass
   * @returns {this} self instance
   */
  Plugin.prototype.updateItems = function (data) {
    var _this3 = this;
    this.$element.empty();
    if (data) {
      // output select options
      this.$element.attr('disabled', false).attr('hidden', false);
      this.$input.attr('disabled', true).attr('hidden', true);
      $.each(data, function (value, name) {
        _this3.$element.append(new Option(name, value, false, false));
      });
    } else {
      // output input field
      this.$element.attr('disabled', true).attr('hidden', true);
      this.$input.attr('disabled', false).attr('hidden', false);
    }
    this.$element.trigger('change');
    return this;
  };
  $.fn[pluginName] = function (options) {
    return this.each(function () {
      if (!$.data(this, pluginName)) {
        $.data(this, pluginName, new Plugin(this, options));
      }
    });
  };
  var init = function init() {
    return $('[data-component~="wcpw-ajax-select"]').each(function () {
      return $(this).wcpwAjaxSelect();
    });
  };
  $document.ready(function () {
    return init();
  });
  $document.on('init.ajaxSelect.wcpw', function () {
    return init();
  });
});