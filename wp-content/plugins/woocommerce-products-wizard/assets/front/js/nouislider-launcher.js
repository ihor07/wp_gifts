function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"]; if (!it) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter); }
function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
/* WooCommerce Products Wizard noUiSlider initialization
 * Original author: mail@troll-winner.ru
 * Further changes, comments: mail@troll-winner.ru
 */

(function (root, factory) {
  'use strict';

  if (typeof define === 'function' && define.amd) {
    define(['wNumb', 'noUiSlider'], factory);
  } else if ((typeof exports === "undefined" ? "undefined" : _typeof(exports)) === 'object' && typeof module !== 'undefined' && typeof require === 'function') {
    module.exports = factory(require('wNumb'), require('noUiSlider'));
  } else {
    factory(root.wNumb, root.noUiSlider);
  }
})(this, function (wNumb, noUiSlider) {
  'use strict';

  var defaultOptions = {
    cssPrefix: 'wcpw-noUi-',
    direction: document.documentElement.getAttribute('dir') || 'ltr',
    range: {
      max: 100,
      min: 0
    },
    start: 50
  };
  function initSlider(element) {
    var bindItems = [];
    var options = [defaultOptions, JSON.parse(element.getAttribute('data-options') || '{}')].reduce(function (output, object) {
      Object.keys(object).forEach(function (key) {
        output[key] = object[key];
      });
      return output;
    }, {});

    // return if there is no range
    if (options.hasOwnProperty('range') && options.range.min === options.range.max) {
      return this;
    }
    element.setAttribute('data-launched', 'true');

    // handle output format
    if (options.hasOwnProperty('format') && typeof wNumb !== 'undefined') {
      options.format = wNumb(options.format);
    }
    noUiSlider.create(element, options);

    // get binding items
    if (options.hasOwnProperty('binding')) {
      if (typeof options.binding === 'string') {
        bindItems.push(options.binding);
      } else if (options.binding instanceof Array) {
        bindItems.push.apply(bindItems, _toConsumableArray(options.binding));
      }
    }
    if (bindItems.length > 0) {
      // handle items change
      bindItems.map(function (bindItem, index) {
        var selector = bindItem;
        if (Array.isArray(bindItem)) {
          var selectors = [];
          bindItem.map(function (bindItemPart) {
            return selectors.push(bindItemPart);
          });
          selector = selectors.join(',');
        }
        bindItems[index] = document.querySelectorAll(selector);

        // bind inputs change
        var _iterator = _createForOfIteratorHelper(bindItems[index]),
          _step;
        try {
          for (_iterator.s(); !(_step = _iterator.n()).done;) {
            var bindElement = _step.value;
            if (['input', 'select', 'textarea'].includes(bindElement.tagName.toLowerCase())) {
              bindElement.addEventListener('change', function () {
                var values = [];
                for (var i = 0; i < index; i++) {
                  values.push(null);
                }
                values.push(this.value);

                // apply value to the slide
                element.noUiSlider.set(values);
              });
            }
          }
        } catch (err) {
          _iterator.e(err);
        } finally {
          _iterator.f();
        }
        return null;
      });
    }

    // on update action
    element.noUiSlider.on('update', function (values, handle) {
      // update bind items
      var _iterator2 = _createForOfIteratorHelper(bindItems[handle]),
        _step2;
      try {
        for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
          var bindElement = _step2.value;
          if (['input', 'select', 'textarea'].includes(bindElement.tagName.toLowerCase())) {
            bindElement.value = values[handle];
          } else {
            bindElement.innerText = values[handle];
          }
        }
      } catch (err) {
        _iterator2.e(err);
      } finally {
        _iterator2.f();
      }
    });
    return this;
  }
  function initSliders() {
    var _iterator3 = _createForOfIteratorHelper(document.querySelectorAll('[data-component~="wcpw-no-ui-slider"]:not([data-launched="true"])')),
      _step3;
    try {
      for (_iterator3.s(); !(_step3 = _iterator3.n()).done;) {
        var element = _step3.value;
        initSlider(element);
      }
    } catch (err) {
      _iterator3.e(err);
    } finally {
      _iterator3.f();
    }
  }
  document.addEventListener('DOMContentLoaded', initSliders);
  document.addEventListener('init.nouislider.wcpw', initSliders);
});