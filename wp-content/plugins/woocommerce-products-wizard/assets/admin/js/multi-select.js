function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
/* WooCommerce Products Wizard Multi-select
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

  var pluginName = 'wcpwMultiSelect';
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
    this.$availableItems = this.$element.find('[data-component~="wcpw-multi-select-items-available"]');
    this.$selectedItems = this.$element.find('[data-component~="wcpw-multi-select-items-selected"]');
    this.$inputs = this.$element.find('[data-component~="wcpw-multi-select-inputs"]');
    return this.initEventListeners();
  };

  /**
   * Add required event listeners
   * @returns {this} self instance
   */
  Plugin.prototype.initEventListeners = function () {
    var _this = this;
    // add the form item
    this.$element.on('click', '[data-component~="wcpw-multi-select-add"]', function (event) {
      event.preventDefault();
      return _this.addItem();
    });

    // remove the form item
    this.$element.on('click', '[data-component~="wcpw-multi-select-remove"]', function (event) {
      event.preventDefault();
      return _this.removeItem();
    });

    // move item upper
    this.$element.on('click', '[data-component~="wcpw-multi-select-move-up"]', function (event) {
      event.preventDefault();
      return _this.moveItemUp();
    });

    // move item lower
    this.$element.on('click', '[data-component~="wcpw-multi-select-move-down"]', function (event) {
      event.preventDefault();
      return _this.moveItemDown();
    });
    return this;
  };

  /**
   * Add the new element in the table
   * @returns {this} self instance
   */
  Plugin.prototype.addItem = function () {
    var _this2 = this;
    this.$availableItems.children(':selected').each(function (i, selected) {
      var $element = $(selected);
      $element.appendTo(_this2.$selectedItems);
      return _this2.$inputs.children("[value=\"".concat($element.val(), "\"]")).removeAttr('disabled');
    });
    this.$element.trigger('added.item.multiSelect.wcpw', [this]);
    return this;
  };

  /**
   * Remove element from the table
   * @returns {this} self instance
   */
  Plugin.prototype.removeItem = function () {
    var _this3 = this;
    this.$selectedItems.children(':selected').each(function (i, selected) {
      var $element = $(selected);
      $element.appendTo(_this3.$availableItems);
      return _this3.$inputs.children("[value=\"".concat($element.val(), "\"]")).attr('disabled', true);
    });
    this.$element.trigger('removed.item.multiSelect.wcpw', [this]);
    return this;
  };

  /**
   * Move element upper in list
   * @returns {this} self instance
   */
  Plugin.prototype.moveItemUp = function () {
    var _this4 = this;
    this.$selectedItems.children(':selected').each(function (i, selected) {
      var $element = $(selected);
      var $input = _this4.$inputs.children("[value=\"".concat($element.val(), "\"]"));
      if ($element.prev().length <= 0) {
        return _this4;
      }
      $element.insertBefore($element.prev());
      $input.insertBefore($input.prev());
      return _this4;
    });
    this.$element.trigger('movedUp.item.multiSelect.wcpw', [this]);
    return this;
  };

  /**
   * Move element lower in list
   * @returns {this} self instance
   */
  Plugin.prototype.moveItemDown = function () {
    var _this5 = this;
    $(this.$selectedItems.children(':selected').get().reverse()).each(function (i, selected) {
      var $element = $(selected);
      var $input = _this5.$inputs.children("[value=\"".concat($element.val(), "\"]"));
      if ($element.next().length <= 0) {
        return _this5;
      }
      $element.insertAfter($element.next());
      $input.insertAfter($input.next());
      return _this5;
    });
    this.$element.trigger('movedDown.item.multiSelect.wcpw', [this]);
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
    return $('[data-component~="wcpw-multi-select"]').each(function () {
      return $(this).wcpwMultiSelect();
    });
  };
  $document.ready(function () {
    return init();
  });
  $document.on('init.multiSelect.wcpw', function () {
    return init();
  });
});