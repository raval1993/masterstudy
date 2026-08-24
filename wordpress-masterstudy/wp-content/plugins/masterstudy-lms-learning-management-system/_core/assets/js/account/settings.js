"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, defineProperty = Object.defineProperty || function (obj, key, desc) { obj[key] = desc.value; }, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return defineProperty(generator, "_invoke", { value: makeInvokeMethod(innerFn, self, context) }), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; defineProperty(this, "_invoke", { value: function value(method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; } function maybeInvokeDelegate(delegate, context) { var methodName = context.method, method = delegate.iterator[methodName]; if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel; var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), defineProperty(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (val) { var object = Object(val), keys = []; for (var key in object) keys.push(key); return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"]; if (!it) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
(function ($) {
  $(document).ready(function () {
    $('.masterstudy-account-settings__avatar-delete').on('click', function () {
      var $this = $(this);
      var $parent = $this.closest('.masterstudy-account-settings__avatar');
      $parent.addClass('masterstudy-account-settings__avatar_loading');
      var formData = new FormData();
      formData.append('action', 'stm_lms_delete_avatar');
      formData.append('nonce', stm_lms_nonces['stm_lms_delete_avatar']);
      $.ajax({
        url: stm_lms_ajaxurl,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function success(data) {
          $parent.removeClass('masterstudy-account-settings__avatar_loading');
          if (data.file) {
            var $wrap = $parent.find('.masterstudy-account-settings__avatar-img');
            var $menu_wrap = $('.masterstudy-account-profile__avatar');
            $wrap.html(data.file);
            $menu_wrap.html(data.file);
            $parent.addClass('masterstudy-account-settings__avatar_no').removeClass('masterstudy-account-settings__avatar_available');
            float_menu_image();
          }
        }
      });
    });
    $('.masterstudy-account-settings__avatar input').on('change', function () {
      var $this = $(this);
      var files = $this[0].files;
      var $parent = $this.closest('.masterstudy-account-settings__avatar');
      $parent.addClass('masterstudy-account-settings__avatar_loading');
      if (files.length) {
        var file = files[0];
        var formData = new FormData();
        formData.append('file', file);
        formData.append('action', 'stm_lms_change_avatar');
        formData.append('nonce', stm_lms_nonces['stm_lms_change_avatar']);
        $.ajax({
          url: stm_lms_ajaxurl,
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          success: function success(data) {
            $parent.removeClass('masterstudy-account-settings__avatar_loading');
            if (data.file) {
              var $wrap = $parent.find('.masterstudy-account-settings__avatar-img');
              var $menu_wrap = $('.masterstudy-account-profile__avatar');
              var $img = $wrap.find('img');
              var $menu_img = $menu_wrap.find('img');
              if ($img.length) {
                $img.attr('src', data.file);
              } else {
                $wrap.html('<img src="' + data.file + '" alt="" />');
              }
              if ($menu_img.length) {
                $menu_img.attr('src', data.file);
              } else {
                $menu_wrap.html('<img src="' + data.file + '" alt="" />');
              }
              $parent.removeClass('masterstudy-account-settings__avatar_no').addClass('masterstudy-account-settings__avatar_available');
              float_menu_image();
            }
          }
        });
      }
    });
    function float_menu_image() {
      var $float_menu = $('.stm_lms_user_float_menu__user');
      if ($float_menu.length) {
        $float_menu.find('img').attr('src', $('.stm-lms-user_avatar').find('img').attr('src'));
      }
    }
    var data = masterstudy_account_settings_data.account_info;
    var additionalFields = [];
    var personal_data = typeof masterstudy_account_settings_data.personal_data !== 'undefined' ? masterstudy_account_settings_data.personal_data : {};
    var i18n = typeof masterstudy_account_settings_data.i18n !== 'undefined' ? masterstudy_account_settings_data.i18n : {};
    var displayNameOptions = new Set();
    var prevCountry, lastNonUSState;
    var displayNameOptionsContainer = $('.masterstudy-account-settings-display-name-options');
    var descriptionInput = $('.masterstudy-account-settings-bio-input');
    var firstNameInput = $('.masterstudy-account-settings-first-name-input');
    var lastNameInput = $('.masterstudy-account-settings-last-name-input');
    var positionInput = $('.masterstudy-account-settings-position-input');
    var newPasswordIcon = $('.masterstudy-account-settings-new-pass-icon');
    var newPasswordInput = $('.masterstudy-account-settings-new-pass-input');
    var reTypeNewPasswordIcon = $('.masterstudy-account-settings-re-new-pass-icon');
    var reTypeNewPasswordInput = $('.masterstudy-account-settings-re-new-pass-input');
    var facebookInput = $('.masterstudy-account-settings-social-facebook-input');
    var linkedinInput = $('.masterstudy-account-settings-social-linkedin-input');
    var twitterInput = $('.masterstudy-account-settings-social-twitter-input');
    var instagramInput = $('.masterstudy-account-settings-social-instagram-input');
    var countrySelect = $('.masterstudy-account-settings-country-select');
    var stateSelect = $('.masterstudy-account-settings-state-select');
    var stateInput = $('.masterstudy-account-settings-state-input');
    var postcodeInput = $('.masterstudy-account-settings-post_code-input');
    var cityInput = $('.masterstudy-account-settings-city-input');
    var companyInput = $('.masterstudy-account-settings-company-input');
    var phoneInput = $('.masterstudy-account-settings-phone-input');
    var saveBtn = $('[data-id="masterstudy-account-settings-save"]');
    var messageEl = $('.masterstudy-account-settings__message');
    var sessionsSection = $('.masterstudy-account-settings__sessions');
    var sessionsMessageEl = $('.masterstudy-account-settings__sessions-message');
    function generateNameOptions(firstName, lastName) {
      if (!firstName || !lastName) return [];
      var fullName1 = firstName + " " + lastName;
      var fullName2 = lastName + " " + firstName;
      return [fullName1, fullName2, firstName, lastName];
    }
    function updateDisplayNameOptions() {
      var initial = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
      var firstName = data.meta.first_name;
      var lastName = data.meta.last_name;
      if (!initial) {
        displayNameOptions.clear();
        $('.masterstudy-account-settings-custom-display-option').remove();
      }
      generateNameOptions(firstName, lastName).forEach(function (name) {
        displayNameOptions.add(name);
      });
      if (!initial) {
        addUniqueNameOptions();
      }
      var _iterator = _createForOfIteratorHelper(displayNameOptions.values()),
        _step;
      try {
        for (_iterator.s(); !(_step = _iterator.n()).done;) {
          var option = _step.value;
          var optionEl = $('<option>', {
            value: option,
            text: option,
            "class": 'masterstudy-account-settings-custom-display-option'
          });
          displayNameOptionsContainer.append(optionEl);
        }
      } catch (err) {
        _iterator.e(err);
      } finally {
        _iterator.f();
      }
    }
    function addUniqueNameOptions() {
      var firstName = data.meta.first_name;
      var lastName = data.meta.last_name;
      generateNameOptions(firstName, lastName).forEach(function (name) {
        displayNameOptions.add(name);
      });
    }
    function togglePasswordEye(iconEl, passwordInput) {
      if (!iconEl.hasClass('masterstudy-account-settings__pass-icon_open')) {
        $(iconEl).addClass('masterstudy-account-settings__pass-icon_open');
        passwordInput.attr('type', 'text');
      } else {
        $(iconEl).removeClass('masterstudy-account-settings__pass-icon_open');
        passwordInput.attr('type', 'password');
      }
    }
    function processFields(fields) {
      if (!Object.keys(fields).length) return;
      var _loop = function _loop(fieldName) {
        if (!fields.hasOwnProperty(fieldName)) return "continue";
        var additionalField = additionalFields.find(function (af) {
          return af.id === fieldName;
        });
        if (!additionalField) return "continue";
        switch (additionalField.type) {
          case 'checkbox':
            {
              var checkedValues = [];
              $("[name=\"".concat(additionalField.slug, "\"]")).each(function () {
                if ($(this).next().hasClass("masterstudy-form-builder__checkbox-wrapper_checked")) {
                  checkedValues.push($(this).val());
                }
              });
              fields[fieldName] = checkedValues.join(",");
              break;
            }
          case 'radio':
            {
              $("[name=\"".concat(additionalField.slug, "\"]")).each(function () {
                if ($(this).next().hasClass("masterstudy-form-builder__radio-wrapper_checked")) {
                  fields[fieldName] = $(this).val();
                }
              });
              break;
            }
          case 'file':
            {
              fields[fieldName] = $("[name=\"".concat(additionalField.slug, "\"]")).attr("data-url") || "";
              break;
            }
          default:
            {
              fields[fieldName] = $("[name=\"".concat(additionalField.slug, "\"]")).val();
            }
        }
      };
      for (var fieldName in fields) {
        var _ret = _loop(fieldName);
        if (_ret === "continue") continue;
      }
    }
    function toggleLoading(val) {
      saveBtn.toggleClass('masterstudy-button_loading', val);
    }
    function toggleMessage(message, status) {
      messageEl.attr('class', 'masterstudy-account-settings__message');
      messageEl.toggleClass('masterstudy-account-settings__message_hidden', !message);
      messageEl.addClass(status);
      messageEl.text(message);
    }
    function toggleSessionsMessage(message, status) {
      sessionsMessageEl.attr('class', 'masterstudy-account-settings__sessions-message');
      sessionsMessageEl.toggleClass('masterstudy-account-settings__sessions-message_hidden', !message);
      sessionsMessageEl.addClass(status);
      sessionsMessageEl.text(message);
    }
    function postSessionAction(_x, _x2, _x3) {
      return _postSessionAction.apply(this, arguments);
    }
    function _postSessionAction() {
      _postSessionAction = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee3(action, nonceKey, payload) {
        var response;
        return _regeneratorRuntime().wrap(function _callee3$(_context3) {
          while (1) switch (_context3.prev = _context3.next) {
            case 0:
              _context3.next = 2;
              return fetch("".concat(stm_lms_ajaxurl, "?action=").concat(action, "&nonce=").concat(stm_lms_nonces[nonceKey]), {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
              });
            case 2:
              response = _context3.sent;
              if (response.ok) {
                _context3.next = 5;
                break;
              }
              throw new Error('not ok');
            case 5:
              return _context3.abrupt("return", response.json());
            case 6:
            case "end":
              return _context3.stop();
          }
        }, _callee3);
      }));
      return _postSessionAction.apply(this, arguments);
    }
    function updateSessionActionsVisibility() {
      var clearButton = sessionsSection.find('[data-id="masterstudy-account-settings-clear-sessions"]');
      var hasOtherSessions = sessionsSection.find('.masterstudy-account-settings__sessions-item').not('.masterstudy-account-settings__sessions-item_current').length > 0;
      clearButton.toggle(hasOtherSessions);
    }
    function ensureSessionEmptyState() {
      var list = sessionsSection.find('.masterstudy-account-settings__sessions-list');
      if (list.find('.masterstudy-account-settings__sessions-item').length === 0 && !list.find('.masterstudy-account-settings__sessions-empty').length) {
        list.append("<div class=\"masterstudy-account-settings__sessions-empty\">".concat(i18n.empty_sessions || '', "</div>"));
      }
    }
    function saveUserInfo() {
      return _saveUserInfo.apply(this, arguments);
    }
    function _saveUserInfo() {
      _saveUserInfo = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee4() {
        var url, pd, allowed, scope, _countrySelect, response, responseData, data_fields, k;
        return _regeneratorRuntime().wrap(function _callee4$(_context4) {
          while (1) switch (_context4.prev = _context4.next) {
            case 0:
              processFields(data.meta);
              url = stm_lms_ajaxurl + "?action=stm_lms_save_user_info&nonce=" + stm_lms_nonces["stm_lms_save_user_info"];
              toggleLoading(true);
              toggleMessage("", "");
              pd = _objectSpread({}, personal_data || {});
              allowed = new Set(['country', 'post_code', 'state', 'city', 'company', 'phone']);
              scope = $('#masterstudy-account-settings');
              scope.find(':input[name]:enabled').each(function () {
                var el = $(this);
                var name = el.attr('name');
                if (!allowed.has(name)) return;
                if (el.is(':checkbox')) {
                  pd[name] = el.is(':checked') ? el.val() || '1' : '0';
                } else if (el.is(':radio')) {
                  if (el.is(':checked')) pd[name] = el.val();
                } else {
                  pd[name] = el.val();
                }
              });
              if (!pd.country && personal_data && personal_data.country) {
                pd.country = personal_data.country;
              } else if (!pd.country) {
                _countrySelect = document.querySelector('[name="country"]');
                if (_countrySelect) pd.country = _countrySelect.value;
              }
              data.meta.personal_data = pd;
              _context4.prev = 10;
              _context4.next = 13;
              return fetch(url, {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json'
                },
                body: JSON.stringify(data.meta)
              });
            case 13:
              response = _context4.sent;
              if (response.ok) {
                _context4.next = 17;
                break;
              }
              toggleMessage('Unable to save settings', 'error');
              return _context4.abrupt("return");
            case 17:
              _context4.next = 19;
              return response.json();
            case 19:
              responseData = _context4.sent;
              if (responseData) {
                _context4.next = 22;
                break;
              }
              return _context4.abrupt("return");
            case 22:
              toggleMessage(responseData.message, responseData.status);
              if (responseData.relogin) {
                window.location.href = responseData.relogin;
              }
              data_fields = {
                bio: "",
                facebook: "href",
                twitter: "href",
                "google-plus": "href",
                position: "",
                first_name: "",
                display_name: "",
                instagram: "href"
              };
              for (k in data_fields) {
                if (data_fields.hasOwnProperty(k)) {
                  if (data_fields[k]) {
                    $(".stm_lms_update_field__" + k).attr(data_fields[k], data["meta"][k]);
                  } else {
                    $(".stm_lms_update_field__" + k).text(data["meta"][k]);
                  }
                }
              }
              _context4.next = 32;
              break;
            case 28:
              _context4.prev = 28;
              _context4.t0 = _context4["catch"](10);
              toggleMessage('Something went wrong while updating settings', 'error');
              console.error(_context4.t0);
            case 32:
              _context4.prev = 32;
              toggleLoading(false);
              return _context4.finish(32);
            case 35:
            case "end":
              return _context4.stop();
          }
        }, _callee4, null, [[10, 28, 32, 35]]);
      }));
      return _saveUserInfo.apply(this, arguments);
    }
    function taxesSetup() {
      prevCountry = countrySelect.length ? (countrySelect.val() || '').toUpperCase() : '';
      lastNonUSState = stateInput.val() || '';
      function initSelect2($el) {
        if (typeof $.fn.select2 === 'function' && !$el.hasClass('select2-hidden-accessible')) {
          $el.select2({
            width: '100%'
          });
        }
      }
      function destroySelect2(el) {
        if (el.hasClass('select2-hidden-accessible')) {
          el.select2('destroy');
        }
      }
      function showStateSelect() {
        syncInputToSelectIfPossible();
        initSelect2(stateSelect);
        stateSelect.prop('disabled', false).show();
        if (stateSelect.next('.select2').length) {
          stateSelect.next('.select2').show();
        }
        stateInput.prop('disabled', true).hide();
      }
      function showStateInput(options) {
        var opts = options || {};
        var preserveExisting = !!opts.preserveExisting;
        if (!preserveExisting) {
          lastNonUSState = stateInput.val() || '';
          stateInput.val(lastNonUSState).trigger('input');
        }
        destroySelect2(stateSelect);
        stateSelect.val('').prop('disabled', true).hide().trigger('change');
        if (stateSelect.next('.select2').length) {
          stateSelect.next('.select2').hide();
        }
        stateInput.prop('disabled', false).show();
      }
      function syncInputToSelectIfPossible() {
        var raw = (stateInput.val() || '').trim();
        if (!raw) return;
        var upper = raw.toUpperCase();
        var opt = stateSelect.find('option[value="' + upper + '"]');
        if (opt.length) {
          stateSelect.val(upper).trigger('change');
          return;
        }
        stateSelect.find('option').each(function () {
          var textUpper = ($(this).text() || '').trim().toUpperCase();
          if (textUpper === upper) {
            stateSelect.val($(this).val()).trigger('change');
            return false;
          }
        });
      }
      function toggleStateField(isInit) {
        var val = (countrySelect.val() || '').toUpperCase();
        if (val === 'US') {
          if (prevCountry !== 'US') {
            lastNonUSState = stateInput.val() || '';
          }
          showStateSelect();
        } else {
          var preserve = !!isInit && prevCountry !== 'US';
          showStateInput({
            preserveExisting: preserve
          });
        }
        prevCountry = val;
      }
      if (stateSelect.length && stateInput.length) {
        if (countrySelect.length) {
          toggleStateField(true);
        } else {
          showStateInput({
            preserveExisting: true
          });
        }
      }
      if (countrySelect.length) {
        countrySelect.on('change', function () {
          toggleStateField(false);
          $(this).removeClass('masterstudy-personal-info-error');
        });
      }
    }
    function init() {
      if (window.profileForm) {
        additionalFields = window.profileForm;
      }
      descriptionInput.val(data.meta.description);
      firstNameInput.val(data.meta.first_name);
      lastNameInput.val(data.meta.last_name);
      displayNameOptionsContainer.val(data.meta.display_name || selectedDisplayName);
      positionInput.val(data.meta.position);
      linkedinInput.val(data.meta.linkedin);
      facebookInput.val(data.meta.facebook);
      twitterInput.val(data.meta.twitter);
      instagramInput.val(data.meta.instagram);
      stateSelect.val(personal_data.state);
      stateInput.val(personal_data.state);
      postcodeInput.val(personal_data.post_code);
      cityInput.val(personal_data.city);
      companyInput.val(personal_data.company);
      phoneInput.val(personal_data.phone);
      countrySelect.val(personal_data.country);
      taxesSetup();
      countrySelect.trigger('change');
      updateDisplayNameOptions(true);
    }
    init();
    descriptionInput.on('input', function () {
      data.meta.description = $(this).val();
    });
    firstNameInput.on('input', function () {
      data.meta.first_name = $(this).val();
      updateDisplayNameOptions(false);
    });
    lastNameInput.on('input', function () {
      data.meta.last_name = $(this).val();
      updateDisplayNameOptions(false);
    });
    displayNameOptionsContainer.on('change', function () {
      data.meta.display_name = $(this).val();
    });
    positionInput.on('input', function () {
      data.meta.position = $(this).val();
    });
    newPasswordIcon.on('click', function () {
      togglePasswordEye($(this), newPasswordInput);
    });
    newPasswordInput.on('input', function () {
      data.meta.new_pass = $(this).val();
    });
    reTypeNewPasswordIcon.on('click', function () {
      togglePasswordEye($(this), reTypeNewPasswordInput);
    });
    reTypeNewPasswordInput.on('input', function () {
      data.meta.new_pass_re = $(this).val();
    });
    facebookInput.on('input', function () {
      data.meta.facebook = $(this).val();
    });
    linkedinInput.on('input', function () {
      data.meta.linkedin = $(this).val();
    });
    twitterInput.on('input', function () {
      data.meta.twitter = $(this).val();
    });
    instagramInput.on('input', function () {
      data.meta.instagram = $(this).val();
    });
    countrySelect.on('change', function () {
      personal_data.country = $(this).val();
    });
    stateInput.on('input', function () {
      personal_data.state = $(this).val();
    });
    postcodeInput.on('input', function () {
      personal_data.post_code = $(this).val();
    });
    cityInput.on('input', function () {
      personal_data.city = $(this).val();
    });
    companyInput.on('input', function () {
      personal_data.company = $(this).val();
    });
    phoneInput.on('input', function () {
      personal_data.phone = $(this).val();
    });
    saveBtn.on('click', function (e) {
      e.preventDefault();
      saveUserInfo();
    });
    $('.masterstudy-account-become-instructor-info__close').on('click', function () {
      var userId = $(this).attr('data-user-id');
      var _this = $(this);
      $.ajax({
        url: stm_lms_ajaxurl,
        type: 'POST',
        dataType: 'json',
        data: {
          'user_id': userId,
          'action': 'stm_lms_hide_become_instructor_notice',
          'nonce': stm_lms_nonces['stm_lms_hide_become_instructor_notice']
        },
        beforeSend: function beforeSend() {
          _this.closest('.masterstudy-account-become-instructor-info').slideUp();
        }
      });
    });
    var $displayNameSelect = $('.masterstudy-account-settings-display-name-options');
    if ($displayNameSelect.length && typeof $.fn.select2 === 'function' && !$displayNameSelect.hasClass('select2-hidden-accessible')) {
      $displayNameSelect.select2({
        minimumResultsForSearch: Infinity,
        width: '100%'
      });
    }
    function setEmailNoticeState(isOn) {
      $('.masterstudy-account-settings-email-notifications').closest('.masterstudy-account-settings__notice-content').toggleClass('masterstudy-account-settings__notice-content_on', !!isOn);
    }
    var $emailToggle = $('.masterstudy-account-settings-email-notifications input[type="checkbox"][name="email_notification"]');
    if ($emailToggle.length) {
      setEmailNoticeState($emailToggle.prop('checked'));
    }
    updateSessionActionsVisibility();
    $(document).on('click', '[data-id="masterstudy-account-settings-signout-session"]', /*#__PURE__*/function () {
      var _ref = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee(e) {
        var button, sessionCard, verifier, response;
        return _regeneratorRuntime().wrap(function _callee$(_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              e.preventDefault();
              button = $(this);
              sessionCard = button.closest('.masterstudy-account-settings__sessions-item');
              verifier = sessionCard.data('verifier');
              if (verifier) {
                _context.next = 6;
                break;
              }
              return _context.abrupt("return");
            case 6:
              if (!(i18n.sign_out_confirm && !window.confirm(i18n.sign_out_confirm))) {
                _context.next = 8;
                break;
              }
              return _context.abrupt("return");
            case 8:
              button.addClass('masterstudy-button_loading');
              toggleSessionsMessage('', '');
              _context.prev = 10;
              _context.next = 13;
              return postSessionAction('stm_lms_destroy_user_session', 'stm_lms_destroy_user_session', {
                verifier: verifier
              });
            case 13:
              response = _context.sent;
              if (!(response.status !== 'success')) {
                _context.next = 17;
                break;
              }
              toggleSessionsMessage(response.message || '', 'error');
              return _context.abrupt("return");
            case 17:
              if (!response.redirect) {
                _context.next = 20;
                break;
              }
              window.location = response.redirect;
              return _context.abrupt("return");
            case 20:
              sessionCard.remove();
              updateSessionActionsVisibility();
              ensureSessionEmptyState();
              toggleSessionsMessage(response.message || '', 'success');
              _context.next = 29;
              break;
            case 26:
              _context.prev = 26;
              _context.t0 = _context["catch"](10);
              toggleSessionsMessage(i18n.sign_out_error || '', 'error');
            case 29:
              _context.prev = 29;
              button.removeClass('masterstudy-button_loading');
              return _context.finish(29);
            case 32:
            case "end":
              return _context.stop();
          }
        }, _callee, this, [[10, 26, 29, 32]]);
      }));
      return function (_x4) {
        return _ref.apply(this, arguments);
      };
    }());
    $(document).on('click', '[data-id="masterstudy-account-settings-clear-sessions"]', /*#__PURE__*/function () {
      var _ref2 = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee2(e) {
        var button, response;
        return _regeneratorRuntime().wrap(function _callee2$(_context2) {
          while (1) switch (_context2.prev = _context2.next) {
            case 0:
              e.preventDefault();
              button = $(this);
              if (!(i18n.clear_all_confirm && !window.confirm(i18n.clear_all_confirm))) {
                _context2.next = 4;
                break;
              }
              return _context2.abrupt("return");
            case 4:
              button.addClass('masterstudy-button_loading');
              toggleSessionsMessage('', '');
              _context2.prev = 6;
              _context2.next = 9;
              return postSessionAction('stm_lms_destroy_other_user_sessions', 'stm_lms_destroy_other_user_sessions', {});
            case 9:
              response = _context2.sent;
              if (!(response.status !== 'success')) {
                _context2.next = 13;
                break;
              }
              toggleSessionsMessage(response.message || '', 'error');
              return _context2.abrupt("return");
            case 13:
              sessionsSection.find('.masterstudy-account-settings__sessions-item').not('.masterstudy-account-settings__sessions-item_current').remove();
              updateSessionActionsVisibility();
              ensureSessionEmptyState();
              toggleSessionsMessage(response.message || '', 'success');
              _context2.next = 22;
              break;
            case 19:
              _context2.prev = 19;
              _context2.t0 = _context2["catch"](6);
              toggleSessionsMessage(i18n.clear_all_error || '', 'error');
            case 22:
              _context2.prev = 22;
              button.removeClass('masterstudy-button_loading');
              return _context2.finish(22);
            case 25:
            case "end":
              return _context2.stop();
          }
        }, _callee2, this, [[6, 19, 22, 25]]);
      }));
      return function (_x5) {
        return _ref2.apply(this, arguments);
      };
    }());
    $(document).on('change', '.masterstudy-account-settings-email-notifications input[type="checkbox"][name="email_notification"]', function () {
      data.meta.disable_report_email_notifications = !this.checked;
      setEmailNoticeState(this.checked);
    });
  });
})(jQuery);