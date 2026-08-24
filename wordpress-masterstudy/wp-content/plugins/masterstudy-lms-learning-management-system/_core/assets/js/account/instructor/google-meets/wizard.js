"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, defineProperty = Object.defineProperty || function (obj, key, desc) { obj[key] = desc.value; }, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return defineProperty(generator, "_invoke", { value: makeInvokeMethod(innerFn, self, context) }), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; defineProperty(this, "_invoke", { value: function value(method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; } function maybeInvokeDelegate(delegate, context) { var methodName = context.method, method = delegate.iterator[methodName]; if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel; var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), defineProperty(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (val) { var object = Object(val), keys = []; for (var key in object) keys.push(key); return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
(function ($) {
  $(document).ready(function () {
    var currentStepTextEl = $('.masterstudy-account-google-meets-wizard__header-steps-value');
    var backBtnEl = $('[data-id="masterstudy-account-google-meets-wizard__back-btn"]');
    var nextBtnEl = $('[data-id="masterstudy-account-google-meets-wizard__next-btn"]');
    var resetCredsBtnEl = $('[data-id="masterstudy-account-google-meets-wizard__reset-creds-btn"]');
    var grantPermBtnEl = $('[data-id="masterstudy-account-google-meets-wizard__grant-permission-btn"]');
    var stepContentEl = $('.masterstudy-account-google-meets-wizard__content');
    var selectors = {
      copyBtn: '[data-id="masterstudy-account-google-meets-wizard__copy-btn"]',
      copyText: '.masterstudy-account-google-meets-wizard__copy-text',
      selectFileBtn: '[data-id="masterstudy-account-google-meets-wizard__select-file"]',
      selectFileInput: '.masterstudy-account-google-meets-wizard__select-file-input',
      selectFileCancel: '.masterstudy-account-google-meets-wizard__select-file-cancel',
      fileName: '.masterstudy-account-google-meets-wizard__file-name'
    };
    var translations = window.stm_google_meet_ajax_variable && window.stm_google_meet_ajax_variable.translations ? window.stm_google_meet_ajax_variable.translations : {};
    var zeros = 0;
    var currentTab = 0;
    var OAuthUrl = '';
    var copyTimeout = 0;
    var credentialsUploaded = false;
    var selectedFileName = '';
    function setButtonDisabled($button, isDisabled) {
      if (isDisabled) {
        $button.addClass('masterstudy-account-google-meets-wizard__btn-disabled');
        return;
      }
      $button.removeClass('masterstudy-account-google-meets-wizard__btn-disabled');
    }
    function setSelectFileName(fileName) {
      var $selectBtnTitle = stepContentEl.find(selectors.selectFileBtn).find('.masterstudy-button__title');
      var $fileName = stepContentEl.find(selectors.fileName);
      if ($selectBtnTitle.length) {
        $selectBtnTitle.text(fileName);
      }
      if ($fileName.length) {
        $fileName.val(fileName);
      }
    }
    function clearSelectFileUi() {
      stepContentEl.find(selectors.selectFileInput).val('');
      selectedFileName = '';
      setSelectFileName('Select file');
      stepContentEl.find(selectors.selectFileBtn).removeClass('masterstudy-account-utility_hidden');
      stepContentEl.find(selectors.fileName).addClass('masterstudy-account-utility_hidden');
      stepContentEl.find(selectors.selectFileCancel).addClass('masterstudy-account-utility_hidden');
    }
    function resetSelectedFileState() {
      credentialsUploaded = false;
      OAuthUrl = '';
      setButtonDisabled(nextBtnEl, true);
      clearSelectFileUi();
    }
    function syncSelectFileUi() {
      if (credentialsUploaded && selectedFileName) {
        setSelectFileName(selectedFileName);
        stepContentEl.find(selectors.selectFileBtn).addClass('masterstudy-account-utility_hidden');
        stepContentEl.find(selectors.fileName).removeClass('masterstudy-account-utility_hidden');
        return;
      }
      clearSelectFileUi();
    }
    function showTab(stepNumber) {
      currentTab = stepNumber;
      currentStepTextEl.text(currentTab + 1);
      var stepTemplate = document.querySelector("#masterstudy-account-google-meets-wizard__step-".concat(currentTab));
      stepContentEl.empty();
      if (stepTemplate) {
        stepContentEl.append(stepTemplate.content.cloneNode(true));
      }
      switch (currentTab) {
        case 0:
          backBtnEl.addClass('masterstudy-account-utility_hidden');
          nextBtnEl.removeClass('masterstudy-account-utility_hidden');
          resetCredsBtnEl.addClass('masterstudy-account-utility_hidden');
          grantPermBtnEl.addClass('masterstudy-account-utility_hidden');
          break;
        case 1:
          backBtnEl.removeClass('masterstudy-account-utility_hidden');
          nextBtnEl.removeClass('masterstudy-account-utility_hidden');
          resetCredsBtnEl.addClass('masterstudy-account-utility_hidden');
          grantPermBtnEl.addClass('masterstudy-account-utility_hidden');
          setButtonDisabled(nextBtnEl, false);
          break;
        case 2:
          backBtnEl.removeClass('masterstudy-account-utility_hidden');
          nextBtnEl.removeClass('masterstudy-account-utility_hidden');
          resetCredsBtnEl.addClass('masterstudy-account-utility_hidden');
          grantPermBtnEl.addClass('masterstudy-account-utility_hidden');
          setButtonDisabled(nextBtnEl, !credentialsUploaded);
          stepContentEl.find(selectors.selectFileCancel).addClass('masterstudy-account-utility_hidden');
          syncSelectFileUi();
          break;
        case 3:
          backBtnEl.addClass('masterstudy-account-utility_hidden');
          nextBtnEl.addClass('masterstudy-account-utility_hidden');
          resetCredsBtnEl.removeClass('masterstudy-account-utility_hidden');
          grantPermBtnEl.removeClass('masterstudy-account-utility_hidden');
          break;
      }
    }
    function resetCredentials() {
      var ajaxData = window.stm_google_meet_ajax_variable;
      credentialsUploaded = false;
      OAuthUrl = '';
      setButtonDisabled(nextBtnEl, true);
      clearSelectFileUi();
      if (!ajaxData) {
        return;
      }
      var formData = new FormData();
      formData.append('action', 'gm_front_reset_settings_ajax');
      formData.append('nonce', ajaxData.nonce);
      $.ajax({
        url: ajaxData.url,
        type: 'post',
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function success() {
          window.location.reload();
        },
        error: function error(xhr) {
          console.error(xhr);
        }
      });
    }
    function isJsonFile(file) {
      if (!file || !file.name) {
        return false;
      }
      var lowerName = file.name.toLowerCase();
      return file.type === 'application/json' || lowerName.endsWith('.json');
    }
    function uploadCredentials(file) {
      var ajaxData = window.stm_google_meet_ajax_variable;
      if (!ajaxData || !file) {
        return;
      }
      if (!isJsonFile(file)) {
        credentialsUploaded = false;
        OAuthUrl = '';
        setButtonDisabled(nextBtnEl, true);
        clearSelectFileUi();
        return;
      }
      var formData = new FormData();
      formData.append('file', file);
      formData.append('action', 'gm_upload_credentials_ajax');
      formData.append('nonce', ajaxData.nonce);
      formData.append('isFront', true);
      setButtonDisabled(nextBtnEl, true);
      $.ajax({
        url: ajaxData.url,
        type: 'post',
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function success(response) {
          OAuthUrl = response && response.url ? response.url : '';
          credentialsUploaded = !!OAuthUrl;
          selectedFileName = file.name;
          setSelectFileName(selectedFileName);
          stepContentEl.find(selectors.selectFileBtn).addClass('masterstudy-account-utility_hidden');
          stepContentEl.find(selectors.fileName).removeClass('masterstudy-account-utility_hidden');
          stepContentEl.find(selectors.selectFileCancel).removeClass('masterstudy-account-utility_hidden');
          setButtonDisabled(nextBtnEl, !credentialsUploaded);
        },
        error: function error(xhr) {
          console.error(xhr);
        }
      });
    }
    function timer() {
      var $timer = $('.stm_countdown');
      if (!$timer.length) {
        return false;
      }
      var ts = $timer.data('timer');
      $timer.countdown({
        timestamp: ts,
        callback: function callback(days, hours, minutes, seconds) {
          var summaryTime = days + hours + minutes + seconds;
          if (summaryTime === 0) {
            zeros++;
          }
          if (zeros === 3) {
            window.location.reload();
          }
        }
      });
    }
    function init() {
      showTab(currentTab);
    }
    init();
    nextBtnEl.on('click', function (event) {
      event.preventDefault();
      if (nextBtnEl.hasClass('masterstudy-account-google-meets-wizard__btn-disabled')) {
        return;
      }
      showTab(currentTab + 1);
    });
    backBtnEl.on('click', function (event) {
      event.preventDefault();
      if (currentTab === 2) {
        resetSelectedFileState();
      }
      showTab(currentTab - 1);
    });
    resetCredsBtnEl.on('click', function (event) {
      event.preventDefault();
      resetCredentials();
    });
    grantPermBtnEl.on('click', function (event) {
      event.preventDefault();
      if (!OAuthUrl) {
        return;
      }
      window.location.href = OAuthUrl;
    });
    stepContentEl.on('click', selectors.selectFileBtn, function (event) {
      event.preventDefault();
      var $input = stepContentEl.find(selectors.selectFileInput);
      if ($input.length) {
        $input.trigger('click');
        return;
      }
    });
    stepContentEl.on('click', selectors.selectFileCancel, function (event) {
      event.preventDefault();
      resetCredentials();
    });
    stepContentEl.on('change', selectors.selectFileInput, function (event) {
      uploadCredentials(event.target.files[0]);
    });
    stepContentEl.on('click', selectors.copyBtn, /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
      var textToCopy, tempInput;
      return _regeneratorRuntime().wrap(function _callee$(_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            clearTimeout(copyTimeout);
            textToCopy = $(selectors.copyText).val();
            if (!window.navigator.clipboard) {
              _context.next = 14;
              break;
            }
            _context.prev = 3;
            _context.next = 6;
            return window.navigator.clipboard.writeText(textToCopy);
          case 6:
            $(selectors.copyBtn).find('.masterstudy-button__title').text(translations.copied);
            copyTimeout = setTimeout(function () {
              $(selectors.copyBtn).find('.masterstudy-button__title').text(translations.copy);
            }, 1000);
            _context.next = 13;
            break;
          case 10:
            _context.prev = 10;
            _context.t0 = _context["catch"](3);
            console.error(_context.t0);
          case 13:
            return _context.abrupt("return");
          case 14:
            tempInput = $('<input>');
            $('body').append(tempInput);
            tempInput.val(textToCopy).select();
            document.execCommand('copy');
            tempInput.remove();
          case 19:
          case "end":
            return _context.stop();
        }
      }, _callee, null, [[3, 10]]);
    })));
    $(window).on('load', function () {
      timer();
    });
  });
})(jQuery);