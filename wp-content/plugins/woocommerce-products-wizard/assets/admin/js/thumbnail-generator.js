function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
/* WooCommerce Products Wizard Thumbnail Generator
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

  var pluginName = 'wcpwThumbnailGenerator';
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
    this.$areasCount = this.$element.find('[data-component~="wcpw-thumbnail-generator-areas-count"]');
    this.$areaSelect = this.$element.find('[data-component~="wcpw-thumbnail-generator-area-select"]');
    this.$canvas = this.$element.find('[data-component~="wcpw-thumbnail-generator-canvas"]');
    this.ajaxUrl = this.$canvas.data('ajax-url') || '/wp-admin/admin-ajax.php';
    this.canvasSize = {
      width: this.$canvas.width(),
      height: this.$canvas.height()
    };
    this.resizableSettings = {
      containment: '[data-component~="wcpw-thumbnail-generator-canvas"]',
      snap: true,
      snapTolerance: 10,
      handles: 'all',
      resize: function resize(event, ui) {
        var $element = $(this).closest('[data-component~="wcpw-thumbnail-generator-area"]');
        $element.find('[data-component~="wcpw-thumbnail-generator-area-width"]').val(ui.size.width);
        $element.find('[data-component~="wcpw-thumbnail-generator-area-height"]').val(ui.size.height);
        event.preventDefault();
      }
    };
    this.draggableSettings = {
      containment: '[data-component~="wcpw-thumbnail-generator-canvas"]',
      snap: true,
      snapTolerance: 10,
      drag: function drag(event, ui) {
        var $element = $(this).closest('[data-component~="wcpw-thumbnail-generator-area"]');
        $element.find('[data-component~="wcpw-thumbnail-generator-area-y"]').val(ui.position.top);
        $element.find('[data-component~="wcpw-thumbnail-generator-area-x"]').val(ui.position.left);
      }
    };
    return this.initEventListeners().initUIHelpers();
  };

  /**
   * Add required event listeners
   * @returns {this} self instance
   */
  Plugin.prototype.initEventListeners = function () {
    var _this2 = this;
    var _this = this;

    // change canvas width
    this.$element.on('change', '#_thumbnail_canvas_width', function () {
      return _this.setCanvasSize({
        width: this.value
      });
    });

    // change canvas height
    this.$element.on('change', '#_thumbnail_canvas_height', function () {
      return _this.setCanvasSize({
        height: this.value
      });
    });

    // clear canvas click
    this.$element.on('click', '[data-component~="wcpw-thumbnail-generator-clear"]', function (event) {
      event.preventDefault();
      return _this2.clearCanvas();
    });

    // add an area
    this.$element.on('click', '[data-component~="wcpw-thumbnail-generator-area-add"]', function (event) {
      event.preventDefault();
      return _this2.addArea();
    });

    // open modal click
    this.$element.on('click', '[data-component~="wcpw-thumbnail-generator-area-settings-modal-open"]', function (event) {
      // eslint-disable-line
      event.preventDefault();
      _this.$element.find(this.getAttribute('href')).addClass('is-opened');
    });

    // close modal click
    this.$element.on('click', '[data-component~="wcpw-thumbnail-generator-area-settings-modal-close"]', function (event) {
      // eslint-disable-line
      event.preventDefault();
      $(this).closest('[data-component~="wcpw-thumbnail-generator-area-settings-modal"]').removeClass('is-opened');
    });

    // clone area click
    this.$element.on('click', '[data-component~="wcpw-thumbnail-generator-area-clone"]', function () {
      return _this.cloneArea($(this).closest('[data-component~="wcpw-thumbnail-generator-area"]'));
    });

    // remove the area
    this.$element.on('click', '[data-component~="wcpw-thumbnail-generator-area-remove"]', function () {
      return _this.removeArea($(this).closest('[data-component~="wcpw-thumbnail-generator-area"]'));
    });

    // change area x position
    this.$element.on('change', '[data-component~="wcpw-thumbnail-generator-area-x"]', function () {
      var $element = $(this);
      var $area = $element.closest('[data-component~="wcpw-thumbnail-generator-area"]');
      var $inner = $area.find('[data-component~="wcpw-thumbnail-generator-area-inner"]');
      var value = Number($element.val());
      if (value + $inner.width() > _this.canvasSize.width) {
        value = _this.canvasSize.width - $inner.width();
        setTimeout(function () {
          return $element.val(value);
        }, 0);
      }
      return _this.setAreaProperty($inner, {
        left: value
      });
    });

    // change area y position
    this.$element.on('change', '[data-component~="wcpw-thumbnail-generator-area-y"]', function () {
      var $element = $(this);
      var $area = $element.closest('[data-component~="wcpw-thumbnail-generator-area"]');
      var $inner = $area.find('[data-component~="wcpw-thumbnail-generator-area-inner"]');
      var value = Number($element.val());
      if (value + $inner.height() > _this.canvasSize.height) {
        value = _this.canvasSize.height - $inner.height();
        setTimeout(function () {
          return $element.val(value);
        }, 0);
      }
      return _this.setAreaProperty($inner, {
        top: value
      });
    });

    // change area width
    this.$element.on('change', '[data-component~="wcpw-thumbnail-generator-area-width"]', function () {
      var $element = $(this);
      var $area = $element.closest('[data-component~="wcpw-thumbnail-generator-area"]');
      var $inner = $area.find('[data-component~="wcpw-thumbnail-generator-area-inner"]');
      var value = Number($element.val());
      if (value + $inner.position().left > _this.canvasSize.width) {
        value = _this.canvasSize.width - $inner.position().left;
        setTimeout(function () {
          return $element.val(value);
        }, 0);
      }
      return _this.setAreaProperty($inner, {
        width: value
      });
    });

    // change area height
    this.$element.on('change', '[data-component~="wcpw-thumbnail-generator-area-height"]', function () {
      var $element = $(this);
      var $area = $element.closest('[data-component~="wcpw-thumbnail-generator-area"]');
      var $inner = $area.find('[data-component~="wcpw-thumbnail-generator-area-inner"]');
      var value = Number($element.val());
      if (value + $inner.position().top > _this.canvasSize.height) {
        value = _this.canvasSize.height - $inner.position().top;
        setTimeout(function () {
          return $element.val(value);
        }, 0);
      }
      return _this.setAreaProperty($inner, {
        height: value
      });
    });

    // change area order
    this.$element.on('click', '[data-component~="wcpw-thumbnail-generator-area-move"]', function (event) {
      event.preventDefault();
      var $element = $(this);
      var $area = $element.closest('[data-component~="wcpw-thumbnail-generator-area"]');
      return _this.changeAreaOrder($area, $element.data('direction'));
    });

    // area settings modal next/prev
    this.$element.on('click', '[data-component~="wcpw-thumbnail-generator-area-settings-modal-select"]', function (event) {
      // eslint-disable-line
      event.preventDefault();
      var $element = $(this);
      var $area = $element.closest('[data-component~="wcpw-thumbnail-generator-area"]');
      if ($area.is(':only-child')) {
        return this;
      }
      var $next = null;
      if ($element.data('direction') === 'next') {
        $next = $area.is(':last-child') ? $area.parent().find('[data-component="wcpw-thumbnail-generator-area"]:first-child') : $area.next();
      } else {
        $next = $area.is(':first-child') ? $area.parent().find('[data-component="wcpw-thumbnail-generator-area"]:last-child') : $area.prev();
      }
      _this.$element.find('[data-component~="wcpw-thumbnail-generator-area-settings-modal"]').removeClass('is-opened');
      $next.find('[data-component~="wcpw-thumbnail-generator-area-settings-modal"]').addClass('is-opened');
      return this;
    });
    return this;
  };

  /**
   * Initial ui helpers
   * @returns {this} self instance
   */
  Plugin.prototype.initUIHelpers = function () {
    var $areas = this.$element.find('[data-component~="wcpw-thumbnail-generator-area-inner"]');
    if ($.fn.draggable) {
      $areas.draggable(this.draggableSettings);
    }
    if ($.fn.resizable) {
      $areas.resizable(this.resizableSettings);
    }
    return this;
  };

  /**
   * Recalculate the input names indexes
   * @returns {this} self instance
   */
  Plugin.prototype.recalculateIds = function () {
    this.$canvas.find('[data-component~="wcpw-thumbnail-generator-area"]').each(function (index) {
      var $element = $(this);
      $element.find('[data-component~="wcpw-thumbnail-generator-area-index"]').val(index);
      return $element.find(':input').each(function () {
        var name = $(this).attr('name');
        if (!name) {
          return this;
        }
        return $(this).attr('name', name.replace(/\[\d+]/g, "[".concat(index, "]")));
      });
    });
    return this;
  };

  /**
   * Change canvas size variables
   * @param {Object} value - width/height values
   * @returns {this} self instance
   */
  Plugin.prototype.setCanvasSize = function (value) {
    var css = {};
    if (typeof value.width !== 'undefined') {
      this.canvasSize.width = value.width;
      css.width = value.width + 'px';
    }
    if (typeof value.height !== 'undefined') {
      this.canvasSize.height = value.height;
      css.height = value.height + 'px';
    }
    this.$canvas.css(css);
    return this;
  };

  /**
   * Clear canvas from areas
   * @returns {this} self instance
   */
  Plugin.prototype.clearCanvas = function () {
    this.$canvas.html('');
    return this.recalculateAreasCount();
  };

  /**
   * Add the new element to the canvas
   * @returns {this} self instance
   */
  Plugin.prototype.addArea = function () {
    var _this3 = this;
    var data = {
      action: 'wcpwGetThumbnailGeneratorAreaView',
      id: this.$areaSelect.val(),
      type: this.$areaSelect.find(':selected').parent().data('type'),
      index: this.$canvas.find('[data-component~="wcpw-thumbnail-generator-area"]').length
    };
    return $.post(this.ajaxUrl, data, function (response) {
      _this3.$canvas.append(response.html);
      _this3.recalculateAreasCount().recalculateIds().initUIHelpers();
      _this3.$element.trigger('added.area.thumbnailGenerator.wcpw', [_this3, response]);
    }, 'json');
  };

  /**
   * Clone area
   * @param {Object} $element - jQuery element
   * @returns {this} self instance
   */
  Plugin.prototype.cloneArea = function ($element) {
    var $clone = $element.clone();
    var $settingsModal = $clone.find('[data-component~="wcpw-thumbnail-generator-area-settings-modal"]');
    var $settingsModalOpen = $clone.find('[data-component~="wcpw-thumbnail-generator-area-settings-modal-open"]');
    var rand = ~~(Math.random() * 1000000);
    $settingsModal.attr('id', "wcpw-thumbnail-generator-area-settings-modal-".concat(rand.toString()));
    $settingsModalOpen.attr('href', "#wcpw-thumbnail-generator-area-settings-modal-".concat(rand.toString()));
    $clone.find('.ui-resizable-handle').remove();
    $clone.insertAfter($element);
    $element.find('[data-component~="wcpw-thumbnail-generator-area-settings-modal"]').removeClass('is-opened');
    this.recalculateAreasCount().recalculateIds().initUIHelpers();
    this.$element.trigger('cloned.area.thumbnailGenerator.wcpw', [this, $clone]);
    return this;
  };

  /**
   * Remove element from the table
   * @param {Object} $area - jQuery element
   * @returns {this} self instance
   */
  Plugin.prototype.removeArea = function ($area) {
    $area.remove();
    this.recalculateAreasCount().recalculateIds();
    this.$element.trigger('removed.area.thumbnailGenerator.wcpw', [this]);
    return this;
  };

  /**
   * Apply css the the area element
   * @param {Object} $element - jQuery element
   * @param {Object} value - top/left/width/height values
   * @param {Boolean} force - set without current value check
   * @returns {this} self instance
   */
  Plugin.prototype.setAreaProperty = function ($element, value) {
    var force = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
    var css = {};
    var elementHeight = $element.height();
    var elementWidth = $element.width();
    var elementPosition = $element.position();
    if (typeof value.top !== 'undefined' && (force || value.top + elementHeight <= this.canvasSize.height && !force)) {
      css.top = value.top + 'px';
    }
    if (typeof value.left !== 'undefined' && (force || value.left + elementWidth <= this.canvasSize.width && !force)) {
      css.left = value.left + 'px';
    }
    if (typeof value.width !== 'undefined' && (force || value.width + elementPosition.left <= this.canvasSize.width && !force)) {
      css.width = value.width + 'px';
    }
    if (typeof value.height !== 'undefined' && (force || value.height + elementPosition.top <= this.canvasSize.height && !force)) {
      css.height = value.height + 'px';
    }
    $element.css(css);
    return this;
  };

  /**
   * Change areas order
   * @param {Object} $element - jQuery element
   * @param {String} direction - up/down
   * @returns {this} self instance
   */
  Plugin.prototype.changeAreaOrder = function ($element, direction) {
    switch (direction) {
      default:
      case 'up':
        if ($element.prev().length > 0) {
          $element.prev().insertAfter($element);
        }
        break;
      case 'down':
        if ($element.next().length > 0) {
          $element.next().insertBefore($element);
        }
    }
    return this.recalculateIds();
  };

  /**
   * Recalculate areas count
   * @returns {this} self instance
   */
  Plugin.prototype.recalculateAreasCount = function () {
    this.$areasCount.text(this.$canvas.find('[data-component~="wcpw-thumbnail-generator-area"]').length);
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
    return $('[data-component~="wcpw-thumbnail-generator-canvas"]').each(function () {
      return $(this).closest('[data-component~="wcpw-settings-group-content"]').wcpwThumbnailGenerator();
    });
  };
  $document.ready(function () {
    return init();
  });
  $document.on('init.thumbnailGenerator.wcpw', function () {
    return init();
  });
});