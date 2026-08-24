"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, defineProperty = Object.defineProperty || function (obj, key, desc) { obj[key] = desc.value; }, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return defineProperty(generator, "_invoke", { value: makeInvokeMethod(innerFn, self, context) }), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; defineProperty(this, "_invoke", { value: function value(method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; } function maybeInvokeDelegate(delegate, context) { var methodName = context.method, method = delegate.iterator[methodName]; if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel; var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), defineProperty(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (val) { var object = Object(val), keys = []; for (var key in object) keys.push(key); return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
(function ($) {
  /**
   * @var stm_lms_fast_login
   */
  $(document).ready(function () {
    var currentMethodEl = $('.stm_lms_fast_login__current-method');
    var switchAccountFormBtn = $('.stm_lms_fast_login__switch-account-link');
    var emailFieldContainer = $('.stm_lms_fast_login__email');
    var emailFieldInput = emailFieldContainer.find('input');
    var passwordFieldContainer = $('.stm_lms_fast_login__password');
    var passwordFieldInput = passwordFieldContainer.find('input');
    var passwordStrengthContainer = $('.stm_lms_fast_login__strength-password');
    var passwordStrengthText = $('.masterstudy-authorization__strength-password__label');
    var showPasswordBtn = $('.stm_lms_fast_login__input-show-pass');
    var fastLoginSubmitBtn = $('.stm_lms_fast_login__submit .masterstudy-button');
    var fastLoginSubmitBtnTitle = fastLoginSubmitBtn.find('.masterstudy-button__title');
    var userPremoderation = !!stm_lms_fast_login['user_premoderation'];
    var formBody = $('.stm_lms_fast_login__body');
    var formHead = $('.stm_lms_fast_login__head');
    var messageWrap = $('.stm_lms_fast_login__message');
    var sessionNotice = messageWrap.find('.masterstudy-authorization__session-notice');
    var messageBox = messageWrap.find('.text-message-register');
    var sessionNoticeComponent = createSessionNoticeComponent();
    var translations = stm_lms_fast_login['translations'];
    var restrictRegistration = stm_lms_fast_login['restrict_registration'];
    var registrationStrengthPassword = stm_lms_fast_login['registration_strength_password'];
    var currentMethod = restrictRegistration ? 'sign-in' : 'sign-up';
    var errors = [];
    var formData = {
      email: '',
      password: ''
    };
    function updateMessageVisibility() {
      var hasVisibleSessionNotice = sessionNotice.length && !sessionNotice.hasClass('masterstudy-authorization__session-notice_hidden');
      var hasVisibleMessage = messageBox.css('display') !== 'none' && messageBox.text().trim() !== '';
      messageWrap.toggle(hasVisibleSessionNotice || hasVisibleMessage);
    }
    function createSessionNoticeComponent() {
      if (!window.masterstudySessionNotice || !sessionNotice.length) {
        return null;
      }
      return window.masterstudySessionNotice.create({
        notice: sessionNotice,
        onBeforeRender: hideGuestCheckoutEmailMessage,
        onChange: updateMessageVisibility,
        getRequestData: function getRequestData(api) {
          return {
            user_login: emailFieldInput.val(),
            user_password: passwordFieldInput.val(),
            recovery_token: api.getRecoveryToken()
          };
        }
      });
    }
    function renderSessionLimitNotice(recoveryToken) {
      hideGuestCheckoutEmailMessage();
      if (!sessionNoticeComponent) {
        updateMessageVisibility();
        return;
      }
      sessionNoticeComponent.setRecoveryToken(recoveryToken || sessionNoticeComponent.getRecoveryToken()).render('', 'warning', true);
    }
    function changeForm(method) {
      hideGuestCheckoutEmailMessage();
      hideSessionNotice();
      formHead.show();
      formBody.show();
      if (method === 'sign-up') {
        currentMethod = 'sign-up';
        currentMethodEl.text(translations.sign_up);
        switchAccountFormBtn.text(translations.sign_in);
        fastLoginSubmitBtnTitle.text(translations.sign_up);
        if (registrationStrengthPassword) {
          passwordStrengthContainer.toggle(true);
        }
      } else {
        currentMethod = 'sign-in';
        currentMethodEl.text(translations.sign_in);
        fastLoginSubmitBtnTitle.text(translations.sign_in);
        if (!restrictRegistration) {
          switchAccountFormBtn.text(translations.sign_up);
        }
        if (registrationStrengthPassword) {
          passwordStrengthContainer.toggle(false);
        }
      }
      formData.email = '';
      formData.password = '';
      passwordFieldInput.val('');
      emailFieldInput.val('');
      errors = [];
      updatePasswordStrength('');
      renderErrors();
    }
    function renderPasswordStrength() {
      Array(4).fill(0).forEach(function (_, index) {
        passwordStrengthContainer.append("<div class=\"stm_lms_fast_login__password-separator masterstudy-authorization__strength-password__separator\" data-index=\"".concat(index, "\"></div>"));
      });
    }
    function hasError(fieldName) {
      return errors.some(function (error) {
        return error.field === fieldName;
      });
    }
    function setIsLoading(val) {
      fastLoginSubmitBtn.toggleClass('loading', val);
    }
    function renderErrors() {
      emailFieldContainer.toggleClass('stm_lms_fast_login__field_has-error', hasError('email'));
      passwordFieldContainer.toggleClass('stm_lms_fast_login__field_has-error', hasError('password'));
      emailFieldContainer.find('.stm_lms_fast_login__error').remove();
      passwordFieldContainer.find('.stm_lms_fast_login__error').remove();
      errors.forEach(function (error) {
        if (error.field === 'email') {
          emailFieldContainer.append("\n                        <span data-id=\"".concat(error.id, "\" class=\"stm_lms_fast_login__error\">\n                            ").concat(error.text, "\n                        </span>\n                    "));
        } else if (error.field === 'password') {
          passwordFieldContainer.append("\n                        <span data-id=\"".concat(error.id, "\" class=\"stm_lms_fast_login__error\">\n                            ").concat(error.text, "\n                        </span>\n                    "));
        }
      });
    }
    function getPasswordStrengthInfo(level) {
      var _masterstudy_authoriz, _masterstudy_authoriz2, _masterstudy_authoriz3, _masterstudy_authoriz4;
      switch (level) {
        case 1:
          return {
            "class": 'bad',
            text: ((_masterstudy_authoriz = masterstudy_authorization_data) === null || _masterstudy_authoriz === void 0 ? void 0 : _masterstudy_authoriz.bad) || "Bad"
          };
        case 2:
          return {
            "class": 'normal',
            text: ((_masterstudy_authoriz2 = masterstudy_authorization_data) === null || _masterstudy_authoriz2 === void 0 ? void 0 : _masterstudy_authoriz2.normal) || "Normal"
          };
        case 3:
          return {
            "class": 'good',
            text: ((_masterstudy_authoriz3 = masterstudy_authorization_data) === null || _masterstudy_authoriz3 === void 0 ? void 0 : _masterstudy_authoriz3.good) || "Good"
          };
        case 4:
          return {
            "class": 'hard',
            text: ((_masterstudy_authoriz4 = masterstudy_authorization_data) === null || _masterstudy_authoriz4 === void 0 ? void 0 : _masterstudy_authoriz4.hard) || "Hard"
          };
        default:
          return {
            "class": '',
            text: ''
          };
      }
    }
    function getPasswordStrength(password) {
      if (!password) return 0;
      var length = password.length;
      var hasLower = /[a-z]/.test(password);
      var hasUpper = /[A-Z]/.test(password);
      var hasNumber = /[0-9]/.test(password);
      if (length >= 8 && length <= 11 && hasLower && hasUpper && hasNumber) {
        return 2;
      }
      if (length >= 12 && length <= 15 && hasLower && hasUpper && hasNumber) {
        return 3;
      }
      if (length >= 16 && hasLower && hasUpper && hasNumber) {
        return 4;
      }
      return 1;
    }
    function updatePasswordStrength(password) {
      var separators = passwordStrengthContainer.find('.stm_lms_fast_login__password-separator');
      var strength = getPasswordStrength(password);
      var strengthInfo = getPasswordStrengthInfo(strength);
      passwordStrengthContainer.attr('class', "masterstudy-authorization__strength-password stm_lms_fast_login__strength-password ".concat(strengthInfo["class"]));
      passwordStrengthText.text(strengthInfo.text);
      separators.each(function (index) {
        $(this).toggleClass('active', index < strength);
      });
    }
    function login() {
      return _login.apply(this, arguments);
    }
    function _login() {
      _login = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
        var url, data, _response$errors, res, response;
        return _regeneratorRuntime().wrap(function _callee$(_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              setIsLoading(true);
              url = stm_lms_ajaxurl + '?action=stm_lms_fast_login&nonce=' + stm_lms_nonces['stm_lms_fast_login'];
              data = {
                user_login: formData.email,
                user_password: formData.password
              };
              renderErrors([]);
              hideGuestCheckoutEmailMessage();
              _context.prev = 5;
              _context.next = 8;
              return fetch(url, {
                method: 'POST',
                body: JSON.stringify(data)
              });
            case 8:
              res = _context.sent;
              if (res.ok) {
                _context.next = 11;
                break;
              }
              return _context.abrupt("return");
            case 11:
              _context.next = 13;
              return res.json();
            case 13:
              response = _context.sent;
              errors = (_response$errors = response['errors']) !== null && _response$errors !== void 0 ? _response$errors : [];
              renderErrors();
              if (!response['session_limit']) {
                _context.next = 19;
                break;
              }
              renderSessionLimitNotice(response['recovery_token']);
              return _context.abrupt("return");
            case 19:
              if (!(response['status'] !== 'error')) {
                _context.next = 24;
                break;
              }
              hideSessionNotice();
              $.removeCookie('stm_lms_notauth_cart', {
                path: '/'
              });
              location.reload();
              return _context.abrupt("return");
            case 24:
              hideSessionNotice();
              _context.next = 30;
              break;
            case 27:
              _context.prev = 27;
              _context.t0 = _context["catch"](5);
              console.error(_context.t0);
            case 30:
              _context.prev = 30;
              setIsLoading(false);
              return _context.finish(30);
            case 33:
            case "end":
              return _context.stop();
          }
        }, _callee, null, [[5, 27, 30, 33]]);
      }));
      return _login.apply(this, arguments);
    }
    function hideGuestCheckoutEmailMessage() {
      messageBox.text('');
      messageBox.hide();
      updateMessageVisibility();
    }
    function showGuestCheckoutEmailMessage(text) {
      hideSessionNotice();
      messageBox.text(text);
      messageBox.show();
      updateMessageVisibility();
    }
    function hideSessionNotice() {
      if (sessionNoticeComponent) {
        sessionNoticeComponent.hide();
      }
      updateMessageVisibility();
    }
    function register() {
      return _register.apply(this, arguments);
    }
    function _register() {
      _register = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee2() {
        var url, data, _response$errors2, res, response;
        return _regeneratorRuntime().wrap(function _callee2$(_context2) {
          while (1) switch (_context2.prev = _context2.next) {
            case 0:
              setIsLoading(true);
              url = stm_lms_ajaxurl + '?action=stm_lms_fast_register&nonce=' + stm_lms_nonces['stm_lms_fast_register'];
              data = {
                email: formData.email,
                password: formData.password
              };
              _context2.prev = 3;
              _context2.next = 6;
              return fetch(url, {
                method: 'POST',
                body: JSON.stringify(data)
              });
            case 6:
              res = _context2.sent;
              if (res.ok) {
                _context2.next = 9;
                break;
              }
              return _context2.abrupt("return");
            case 9:
              _context2.next = 11;
              return res.json();
            case 11:
              response = _context2.sent;
              errors = (_response$errors2 = response['errors']) !== null && _response$errors2 !== void 0 ? _response$errors2 : [];
              renderErrors();
              if (!(response.status !== 'error')) {
                _context2.next = 22;
                break;
              }
              $.removeCookie('stm_lms_notauth_cart', {
                path: '/'
              });
              if (!userPremoderation) {
                _context2.next = 21;
                break;
              }
              formHead.hide();
              formBody.hide();
              showGuestCheckoutEmailMessage(response.message || stm_lms_fast_login['confirmation_message']);
              return _context2.abrupt("return");
            case 21:
              location.reload();
            case 22:
              _context2.next = 27;
              break;
            case 24:
              _context2.prev = 24;
              _context2.t0 = _context2["catch"](3);
              console.error(_context2.t0);
            case 27:
              _context2.prev = 27;
              setIsLoading(false);
              return _context2.finish(27);
            case 30:
            case "end":
              return _context2.stop();
          }
        }, _callee2, null, [[3, 24, 27, 30]]);
      }));
      return _register.apply(this, arguments);
    }
    function init() {
      changeForm(currentMethod);
      if (registrationStrengthPassword) {
        renderPasswordStrength();
      }
    }
    init();
    switchAccountFormBtn.on('click', function (e) {
      e.preventDefault();
      changeForm(currentMethod === 'sign-up' ? 'sign-in' : 'sign-up');
    });
    emailFieldInput.on('input', function () {
      formData.email = $(this).val();
    });
    passwordFieldInput.on('input', function () {
      formData.password = $(this).val();
      if (registrationStrengthPassword && currentMethod === 'sign-up') {
        updatePasswordStrength(formData.password);
      }
    });
    showPasswordBtn.on('click', function () {
      var type = passwordFieldInput.attr('type');
      passwordFieldInput.attr('type', type === 'password' ? 'text' : 'password');
      $(this).toggleClass('stm_lms_fast_login__input-show-pass_open', type === 'password');
    });
    fastLoginSubmitBtn.on('click', function (e) {
      e.preventDefault();
      if (currentMethod === 'sign-in') {
        login();
      } else {
        register();
      }
    });
  });
})(jQuery);