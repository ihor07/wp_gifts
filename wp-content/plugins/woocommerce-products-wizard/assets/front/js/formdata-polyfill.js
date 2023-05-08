function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _iterableToArrayLimit(arr, i) { var _i = null == arr ? null : "undefined" != typeof Symbol && arr[Symbol.iterator] || arr["@@iterator"]; if (null != _i) { var _s, _e, _x, _r, _arr = [], _n = !0, _d = !1; try { if (_x = (_i = _i.call(arr)).next, 0 === i) { if (Object(_i) !== _i) return; _n = !1; } else for (; !(_n = (_s = _x.call(_i)).done) && (_arr.push(_s.value), _arr.length !== i); _n = !0) { ; } } catch (err) { _d = !0, _e = err; } finally { try { if (!_n && null != _i["return"] && (_r = _i["return"](), Object(_r) !== _r)) return; } finally { if (_d) throw _e; } } return _arr; } }
function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }
function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"]; if (!it) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e2) { throw _e2; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e3) { didErr = true; err = _e3; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, defineProperty = Object.defineProperty || function (obj, key, desc) { obj[key] = desc.value; }, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return defineProperty(generator, "_invoke", { value: makeInvokeMethod(innerFn, self, context) }), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; defineProperty(this, "_invoke", { value: function value(method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; } function maybeInvokeDelegate(delegate, context) { var methodName = context.method, method = delegate.iterator[methodName]; if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel; var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) { if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; } return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), defineProperty(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (val) { var object = Object(val), keys = []; for (var key in object) { keys.push(key); } return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) { "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); } }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
/* formdata-polyfill. MIT License. Jimmy Wärting <https://jimmy.warting.se/opensource> */
/* https://github.com/jimmywarting/FormData */
/* global FormData self Blob File */
/* eslint-disable */

if (typeof Blob !== 'undefined' && (typeof FormData === 'undefined' || !FormData.prototype.keys)) {
  var ensureArgs = function ensureArgs(args, expected) {
    if (args.length < expected) {
      throw new TypeError("".concat(expected, " argument required, but only ").concat(args.length, " present."));
    }
  };
  var normalizeArgs = function normalizeArgs(name, value, filename) {
    if (value instanceof Blob) {
      filename = filename !== undefined ? String(filename + '') : typeof value.name === 'string' ? value.name : 'blob';
      if (value.name !== filename || Object.prototype.toString.call(value) === '[object Blob]') {
        value = new File([value], filename);
      }
      return [String(name), value];
    }
    return [String(name), String(value)];
  }; // normalize line feeds for textarea
  // https://html.spec.whatwg.org/multipage/form-elements.html#textarea-line-break-normalisation-transformation
  var normalizeLinefeeds = function normalizeLinefeeds(value) {
    return value.replace(/\r?\n|\r/g, '\r\n');
  };
  var each = function each(arr, cb) {
    for (var i = 0; i < arr.length; i++) {
      cb(arr[i]);
    }
  };
  'use strict';
  var global = (typeof globalThis === "undefined" ? "undefined" : _typeof(globalThis)) === 'object' ? globalThis : (typeof window === "undefined" ? "undefined" : _typeof(window)) === 'object' ? window : (typeof self === "undefined" ? "undefined" : _typeof(self)) === 'object' ? self : this;

  // keep a reference to native implementation
  var _FormData = global.FormData;

  // To be monkey patched
  var _send = global.XMLHttpRequest && global.XMLHttpRequest.prototype.send;
  var _fetch = global.Request && global.fetch;
  var _sendBeacon = global.navigator && global.navigator.sendBeacon;
  // Might be a worker thread...
  var _match = global.Element && global.Element.prototype;

  // Unable to patch Request/Response constructor correctly #109
  // only way is to use ES6 class extend
  // https://github.com/babel/babel/issues/1966

  var stringTag = global.Symbol && Symbol.toStringTag;

  // Add missing stringTags to blob and files
  if (stringTag) {
    if (!Blob.prototype[stringTag]) {
      Blob.prototype[stringTag] = 'Blob';
    }
    if ('File' in global && !File.prototype[stringTag]) {
      File.prototype[stringTag] = 'File';
    }
  }

  // Fix so you can construct your own File
  try {
    new File([], ''); // eslint-disable-line
  } catch (a) {
    global.File = function File(b, d, c) {
      var blob = new Blob(b, c || {});
      var t = c && void 0 !== c.lastModified ? new Date(c.lastModified) : new Date();
      Object.defineProperties(blob, {
        name: {
          value: d
        },
        lastModified: {
          value: +t
        },
        toString: {
          value: function value() {
            return '[object File]';
          }
        }
      });
      if (stringTag) {
        Object.defineProperty(blob, stringTag, {
          value: 'File'
        });
      }
      return blob;
    };
  }
  var _escape = function _escape(str) {
    return str.replace(/\n/g, '%0A').replace(/\r/g, '%0D').replace(/"/g, '%22');
  };

  /**
   * @implements {Iterable}
   */
  var FormDataPolyfill = /*#__PURE__*/function (_Symbol$iterator) {
    /**
     * FormData class
     *
     * @param {HTMLElement=} form
     */
    function FormDataPolyfill(form) {
      _classCallCheck(this, FormDataPolyfill);
      this._data = [];
      var self = this;
      form && each(form.elements, function (elm) {
        if (!elm.name || elm.disabled || elm.type === 'submit' || elm.type === 'button' || elm.matches('form fieldset[disabled] *')) return;
        if (elm.type === 'file') {
          var files = elm.files && elm.files.length ? elm.files : [new File([], '', {
            type: 'application/octet-stream'
          })]; // #78

          each(files, function (file) {
            self.append(elm.name, file);
          });
        } else if (elm.type === 'select-multiple' || elm.type === 'select-one') {
          each(elm.options, function (opt) {
            !opt.disabled && opt.selected && self.append(elm.name, opt.value);
          });
        } else if (elm.type === 'checkbox' || elm.type === 'radio') {
          if (elm.checked) self.append(elm.name, elm.value);
        } else {
          var value = elm.type === 'textarea' ? normalizeLinefeeds(elm.value) : elm.value;
          self.append(elm.name, value);
        }
      });
    }

    /**
     * Append a field
     *
     * @param   {string}           name      field name
     * @param   {string|Blob|File} value     string / blob / file
     * @param   {string=}          filename  filename to use with blob
     * @return  {undefined}
     */
    _createClass(FormDataPolyfill, [{
      key: "append",
      value: function append(name, value, filename) {
        ensureArgs(arguments, 2);
        this._data.push(normalizeArgs(name, value, filename));
      }

      /**
       * Delete all fields values given name
       *
       * @param   {string}  name  Field name
       * @return  {undefined}
       */
    }, {
      key: "delete",
      value: function _delete(name) {
        ensureArgs(arguments, 1);
        var result = [];
        name = String(name);
        each(this._data, function (entry) {
          entry[0] !== name && result.push(entry);
        });
        this._data = result;
      }

      /**
       * Iterate over all fields as [name, value]
       *
       * @return {Iterator}
       */
    }, {
      key: "entries",
      value:
      /*#__PURE__*/
      _regeneratorRuntime().mark(function entries() {
        var i;
        return _regeneratorRuntime().wrap(function entries$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                i = 0;
              case 1:
                if (!(i < this._data.length)) {
                  _context.next = 7;
                  break;
                }
                _context.next = 4;
                return this._data[i];
              case 4:
                i++;
                _context.next = 1;
                break;
              case 7:
              case "end":
                return _context.stop();
            }
          }
        }, entries, this);
      })
      /**
       * Iterate over all fields
       *
       * @param   {Function}  callback  Executed for each item with parameters (value, name, thisArg)
       * @param   {Object=}   thisArg   `this` context for callback function
       * @return  {undefined}
       */
    }, {
      key: "forEach",
      value: function forEach(callback, thisArg) {
        ensureArgs(arguments, 1);
        var _iterator = _createForOfIteratorHelper(this),
          _step;
        try {
          for (_iterator.s(); !(_step = _iterator.n()).done;) {
            var _step$value = _slicedToArray(_step.value, 2),
              name = _step$value[0],
              value = _step$value[1];
            callback.call(thisArg, value, name, this);
          }
        } catch (err) {
          _iterator.e(err);
        } finally {
          _iterator.f();
        }
      }

      /**
       * Return first field value given name
       * or null if non existent
       *
       * @param   {string}  name      Field name
       * @return  {string|File|null}  value Fields value
       */
    }, {
      key: "get",
      value: function get(name) {
        ensureArgs(arguments, 1);
        var entries = this._data;
        name = String(name);
        for (var i = 0; i < entries.length; i++) {
          if (entries[i][0] === name) {
            return entries[i][1];
          }
        }
        return null;
      }

      /**
       * Return all fields values given name
       *
       * @param   {string}  name  Fields name
       * @return  {Array}         [{String|File}]
       */
    }, {
      key: "getAll",
      value: function getAll(name) {
        ensureArgs(arguments, 1);
        var result = [];
        name = String(name);
        each(this._data, function (data) {
          data[0] === name && result.push(data[1]);
        });
        return result;
      }

      /**
       * Check for field name existence
       *
       * @param   {string}   name  Field name
       * @return  {boolean}
       */
    }, {
      key: "has",
      value: function has(name) {
        ensureArgs(arguments, 1);
        name = String(name);
        for (var i = 0; i < this._data.length; i++) {
          if (this._data[i][0] === name) {
            return true;
          }
        }
        return false;
      }

      /**
       * Iterate over all fields name
       *
       * @return {Iterator}
       */
    }, {
      key: "keys",
      value:
      /*#__PURE__*/
      _regeneratorRuntime().mark(function keys() {
        var _iterator2, _step2, _step2$value, name;
        return _regeneratorRuntime().wrap(function keys$(_context2) {
          while (1) {
            switch (_context2.prev = _context2.next) {
              case 0:
                _iterator2 = _createForOfIteratorHelper(this);
                _context2.prev = 1;
                _iterator2.s();
              case 3:
                if ((_step2 = _iterator2.n()).done) {
                  _context2.next = 9;
                  break;
                }
                _step2$value = _slicedToArray(_step2.value, 1), name = _step2$value[0];
                _context2.next = 7;
                return name;
              case 7:
                _context2.next = 3;
                break;
              case 9:
                _context2.next = 14;
                break;
              case 11:
                _context2.prev = 11;
                _context2.t0 = _context2["catch"](1);
                _iterator2.e(_context2.t0);
              case 14:
                _context2.prev = 14;
                _iterator2.f();
                return _context2.finish(14);
              case 17:
              case "end":
                return _context2.stop();
            }
          }
        }, keys, this, [[1, 11, 14, 17]]);
      })
      /**
       * Overwrite all values given name
       *
       * @param   {string}    name      Filed name
       * @param   {string}    value     Field value
       * @param   {string=}   filename  Filename (optional)
       * @return  {undefined}
       */
    }, {
      key: "set",
      value: function set(name, value, filename) {
        ensureArgs(arguments, 2);
        name = String(name);
        var result = [];
        var args = normalizeArgs(name, value, filename);
        var replace = true;

        // - replace the first occurrence with same name
        // - discards the remaining with same name
        // - while keeping the same order items where added
        each(this._data, function (data) {
          data[0] === name ? replace && (replace = !result.push(args)) : result.push(data);
        });
        replace && result.push(args);
        this._data = result;
      }

      /**
       * Iterate over all fields
       *
       * @return {Iterator}
       */
    }, {
      key: "values",
      value:
      /*#__PURE__*/
      _regeneratorRuntime().mark(function values() {
        var _iterator3, _step3, _step3$value, value;
        return _regeneratorRuntime().wrap(function values$(_context3) {
          while (1) {
            switch (_context3.prev = _context3.next) {
              case 0:
                _iterator3 = _createForOfIteratorHelper(this);
                _context3.prev = 1;
                _iterator3.s();
              case 3:
                if ((_step3 = _iterator3.n()).done) {
                  _context3.next = 9;
                  break;
                }
                _step3$value = _slicedToArray(_step3.value, 2), value = _step3$value[1];
                _context3.next = 7;
                return value;
              case 7:
                _context3.next = 3;
                break;
              case 9:
                _context3.next = 14;
                break;
              case 11:
                _context3.prev = 11;
                _context3.t0 = _context3["catch"](1);
                _iterator3.e(_context3.t0);
              case 14:
                _context3.prev = 14;
                _iterator3.f();
                return _context3.finish(14);
              case 17:
              case "end":
                return _context3.stop();
            }
          }
        }, values, this, [[1, 11, 14, 17]]);
      })
      /**
       * Return a native (perhaps degraded) FormData with only a `append` method
       * Can throw if it's not supported
       *
       * @return {FormData}
       */
    }, {
      key: '_asNative',
      value: function _asNative() {
        var fd = new _FormData();
        var _iterator4 = _createForOfIteratorHelper(this),
          _step4;
        try {
          for (_iterator4.s(); !(_step4 = _iterator4.n()).done;) {
            var _step4$value = _slicedToArray(_step4.value, 2),
              name = _step4$value[0],
              value = _step4$value[1];
            fd.append(name, value);
          }
        } catch (err) {
          _iterator4.e(err);
        } finally {
          _iterator4.f();
        }
        return fd;
      }

      /**
       * [_blob description]
       *
       * @return {Blob} [description]
       */
    }, {
      key: '_blob',
      value: function _blob() {
        var boundary = '----formdata-polyfill-' + Math.random(),
          chunks = [],
          p = "--".concat(boundary, "\r\nContent-Disposition: form-data; name=\"");
        this.forEach(function (value, name) {
          return typeof value == 'string' ? chunks.push(p + _escape(normalizeLinefeeds(name)) + "\"\r\n\r\n".concat(normalizeLinefeeds(value), "\r\n")) : chunks.push(p + _escape(normalizeLinefeeds(name)) + "\"; filename=\"".concat(_escape(value.name), "\"\r\nContent-Type: ").concat(value.type || "application/octet-stream", "\r\n\r\n"), value, "\r\n");
        });
        chunks.push("--".concat(boundary, "--"));
        return new Blob(chunks, {
          type: "multipart/form-data; boundary=" + boundary
        });
      }

      /**
       * The class itself is iterable
       * alias for formdata.entries()
       *
       * @return  {Iterator}
       */
    }, {
      key: _Symbol$iterator,
      value: function value() {
        return this.entries();
      }

      /**
       * Create the default string description.
       *
       * @return  {string} [object FormData]
       */
    }, {
      key: "toString",
      value: function toString() {
        return '[object FormData]';
      }
    }]);
    return FormDataPolyfill;
  }(Symbol.iterator);
  if (_match && !_match.matches) {
    _match.matches = _match.matchesSelector || _match.mozMatchesSelector || _match.msMatchesSelector || _match.oMatchesSelector || _match.webkitMatchesSelector || function (s) {
      var matches = (this.document || this.ownerDocument).querySelectorAll(s);
      var i = matches.length;
      while (--i >= 0 && matches.item(i) !== this) {}
      return i > -1;
    };
  }
  if (stringTag) {
    /**
     * Create the default string description.
     * It is accessed internally by the Object.prototype.toString().
     */
    FormDataPolyfill.prototype[stringTag] = 'FormData';
  }

  // Patch xhr's send method to call _blob transparently
  if (_send) {
    var setRequestHeader = global.XMLHttpRequest.prototype.setRequestHeader;
    global.XMLHttpRequest.prototype.setRequestHeader = function (name, value) {
      setRequestHeader.call(this, name, value);
      if (name.toLowerCase() === 'content-type') this._hasContentType = true;
    };
    global.XMLHttpRequest.prototype.send = function (data) {
      // need to patch send b/c old IE don't send blob's type (#44)
      if (data instanceof FormDataPolyfill) {
        var blob = data['_blob']();
        if (!this._hasContentType) this.setRequestHeader('Content-Type', blob.type);
        _send.call(this, blob);
      } else {
        _send.call(this, data);
      }
    };
  }

  // Patch fetch's function to call _blob transparently
  if (_fetch) {
    global.fetch = function (input, init) {
      if (init && init.body && init.body instanceof FormDataPolyfill) {
        init.body = init.body['_blob']();
      }
      return _fetch.call(this, input, init);
    };
  }

  // Patch navigator.sendBeacon to use native FormData
  if (_sendBeacon) {
    global.navigator.sendBeacon = function (url, data) {
      if (data instanceof FormDataPolyfill) {
        data = data['_asNative']();
      }
      return _sendBeacon.call(this, url, data);
    };
  }
  global['FormData'] = FormDataPolyfill;
}