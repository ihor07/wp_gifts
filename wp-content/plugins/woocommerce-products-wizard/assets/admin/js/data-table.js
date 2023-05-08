function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
/* WooCommerce Products Wizard Data Table
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

  var pluginName = 'wcpwDataTable';
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
    var _this2 = this;
    this.$element = $(this.element);
    this.$body = this.$element.children('table').children('tbody');
    this.$template = this.$element.children('fieldset').children('table').children('tbody').children('[data-component~="wcpw-data-table-item"]');
    if ($.fn.sortable) {
      // init sortable
      this.$element.sortable({
        items: '[data-component~="wcpw-data-table-item"]',
        distance: 50,
        update: function update() {
          return _this2.recalculateIds();
        },
        cancel: 'input,textarea,button,select,option,.wcpw-modal'
      });
    }
    return this.initEventListeners();
  };

  /**
   * Add required event listeners
   * @returns {this} self instance
   */
  Plugin.prototype.initEventListeners = function () {
    var _this = this;

    // add the form item
    this.$element.on('click', '[data-component~="wcpw-data-table-item-add"]', function () {
      return _this.addItem($(this).closest('[data-component~="wcpw-data-table-item"]'));
    });

    // remove the form item
    this.$element.on('click', '[data-component~="wcpw-data-table-item-remove"]', function () {
      return _this.removeItem($(this).closest('[data-component~="wcpw-data-table-item"]'));
    });

    // clone the form item
    this.$element.on('click', '[data-component~="wcpw-data-table-item-clone"]', function () {
      return _this.cloneItem($(this).closest('[data-component~="wcpw-data-table-item"]'));
    });
    return this;
  };

  /**
   * Add a new element in the table
   * @param {Object} $insertAfterItem - jQuery element
   * @param {Object} $template - jQuery element
   * @returns {Object} jQuery clone element
   */
  Plugin.prototype.addItem = function ($insertAfterItem) {
    var $template = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : this.$template;
    $insertAfterItem = $insertAfterItem.length !== 0 ? $insertAfterItem : this.$body.children('[data-component~="wcpw-data-table-item"]:last');
    if ($template.length === 0) {
      $template = $insertAfterItem;
    }

    // clone the new element from default template
    var $clone = $template.clone();
    $clone.find(':input').each(function () {
      var _this3 = this;
      // make real attributes from the placeholders
      $.each(this.attributes, function (i, attr) {
        var name = attr.name;
        var value = attr.value;
        if (name.indexOf('data-make') === -1) {
          return _this3;
        }
        return _this3.setAttribute(name.replace('data-make-', ''), value);
      });
    });
    var $settingsModal = $clone.find('[data-component~="wcpw-data-table-item-modal"]');
    var $openSettingsModal = $clone.find('[data-component~="wcpw-data-table-item-open-modal"]');
    var rand = Math.random().toString(36).substr(2);
    $settingsModal.attr('id', $settingsModal.attr('id') + "-".concat(rand.toString()));
    $openSettingsModal.attr('href', $openSettingsModal.attr('href') + "-".concat(rand.toString()));

    // insert the clone element
    $clone.insertAfter($insertAfterItem);
    this.recalculateIds();
    this.$element.trigger('added.item.dataTable.wcpw', [this, $clone]);
    return $clone;
  };

  /**
   * Remove element from the table
   * @param {Object} $item - jQuery element
   * @returns {this} self instance
   */
  Plugin.prototype.removeItem = function ($item) {
    if ($item.is(':only-child')) {
      // clear values of the last item
      $item.find(':input').val('').trigger('change');
    } else {
      // remove the non-last item
      $item.remove();
    }
    this.recalculateIds();
    this.$element.trigger('removed.item.dataTable.wcpw', [this]);
    return this;
  };

  /**
   * Clone element from the table
   * @param {Object} $item - jQuery element
   * @returns {this} self instance
   */
  Plugin.prototype.cloneItem = function ($item) {
    var $clone = this.addItem($item, $item);
    this.$element.trigger('cloned.item.dataTable.wcpw', [this, $clone]);
    return this;
  };

  /**
   * Recalculate the input names indexes
   * @returns {this} self instance
   */
  Plugin.prototype.recalculateIds = function () {
    this.$body.children('[data-component~="wcpw-data-table-item"]').each(function (index) {
      return $(this).find(':input').each(function () {
        var name = this.getAttribute('name');
        if (name) {
          var number = index.toString().split('').reverse().join(''); // eslint-disable-line

          // replace the first array key from the end
          this.setAttribute('name', name.split('').reverse().join('').replace(/]\d+\[/, "]".concat(number, "[")).split('').reverse().join(''));
        }
      });
    });
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
    return $('[data-component~="wcpw-data-table"]').each(function () {
      return $(this).wcpwDataTable();
    });
  };
  $document.ready(function () {
    return init();
  });
  $document.on('init.dataTable.wcpw', function () {
    return init();
  });
});