"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, defineProperty = Object.defineProperty || function (obj, key, desc) { obj[key] = desc.value; }, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return defineProperty(generator, "_invoke", { value: makeInvokeMethod(innerFn, self, context) }), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; defineProperty(this, "_invoke", { value: function value(method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; } function maybeInvokeDelegate(delegate, context) { var methodName = context.method, method = delegate.iterator[methodName]; if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel; var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), defineProperty(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (val) { var object = Object(val), keys = []; for (var key in object) keys.push(key); return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
function _iterableToArrayLimit(arr, i) { var _i = null == arr ? null : "undefined" != typeof Symbol && arr[Symbol.iterator] || arr["@@iterator"]; if (null != _i) { var _s, _e, _x, _r, _arr = [], _n = !0, _d = !1; try { if (_x = (_i = _i.call(arr)).next, 0 === i) { if (Object(_i) !== _i) return; _n = !1; } else for (; !(_n = (_s = _x.call(_i)).done) && (_arr.push(_s.value), _arr.length !== i); _n = !0); } catch (err) { _d = !0, _e = err; } finally { try { if (!_n && null != _i["return"] && (_r = _i["return"](), Object(_r) !== _r)) return; } finally { if (_d) throw _e; } } return _arr; } }
function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
(function ($) {
  $(document).ready(function () {
    new Vue({
      el: '#stm_lms_checkout',
      data: function data() {
        return {
          loading: false,
          messages: [],
          status: 'error',
          payment_code: '',
          stripe: '',
          stripe_card: '',
          stripe_complete: false,
          agree_with_policy: false,
          coupon_applied: false,
          coupon: null,
          coupon_applied_item_ids: [],
          coupon_applied_subtotal: 0
        };
      },
      methods: {
        toggle_policy: function toggle_policy() {
          this.agree_with_policy = !this.agree_with_policy;
        },
        purchase_courses: function purchase_courses() {
          if (this.loading || !this.agree_with_policy) return false;
          var vm = this;
          vm.loading = true;
          vm.messages = [];
          function handleResponse(_x) {
            return _handleResponse.apply(this, arguments);
          }
          function _handleResponse() {
            _handleResponse = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee(response) {
              var data, result;
              return _regeneratorRuntime().wrap(function _callee$(_context) {
                while (1) switch (_context.prev = _context.next) {
                  case 0:
                    vm.status = response.body.status;
                    jQuery('.masterstudy-personal-info .masterstudy-personal-info-error').removeClass('masterstudy-personal-info-error');
                    if (response.body.status === 'personal_data_error' && Array.isArray(response.body.errors)) {
                      vm.messages = response.body.errors.map(function (e) {
                        if (e.field) {
                          var $field = jQuery(".masterstudy-personal-info [name=\"".concat(e.field, "\"]"));
                          if ($field.length) {
                            $field.addClass('masterstudy-personal-info-error');
                          }
                        }
                        return e.message;
                      });
                    } else if (response.body.message) {
                      vm.messages = [response.body.message];
                    } else {
                      vm.messages = [];
                    }
                    data = {
                      event_type: "order_created",
                      payment_code: vm.payment_code,
                      url: response.body.url || ""
                    };
                    stm_lms_print_message(data);
                    _context.prev = 5;
                    if (!(vm.payment_code === 'stripe' && response.body.client_secret)) {
                      _context.next = 16;
                      break;
                    }
                    _context.next = 9;
                    return vm.stripe.confirmCardPayment(response.body.client_secret, {
                      payment_method: {
                        card: vm.stripe_card
                      }
                    });
                  case 9:
                    result = _context.sent;
                    if (!result.error) {
                      _context.next = 12;
                      break;
                    }
                    return _context.abrupt("return", handleError(result.error));
                  case 12:
                    if (!(result.paymentIntent && result.paymentIntent.status === 'succeeded')) {
                      _context.next = 16;
                      break;
                    }
                    if (!response.body.url) {
                      _context.next = 16;
                      break;
                    }
                    window.location = response.body.url;
                    return _context.abrupt("return");
                  case 16:
                    _context.next = 21;
                    break;
                  case 18:
                    _context.prev = 18;
                    _context.t0 = _context["catch"](5);
                    return _context.abrupt("return", handleError(_context.t0));
                  case 21:
                    if (response.body.url) {
                      window.location = response.body.url;
                    } else {
                      vm.loading = false;
                    }
                  case 22:
                  case "end":
                    return _context.stop();
                }
              }, _callee, null, [[5, 18]]);
            }));
            return _handleResponse.apply(this, arguments);
          }
          function handleError(error) {
            vm.loading = false;
            vm.status = 'error';
            vm.messages = [error.message || 'An error occurred'];
          }
          var personal_data = {};
          jQuery('.masterstudy-personal-info :input[name]:enabled').each(function () {
            var $el = jQuery(this);
            var name = $el.attr('name');
            if ($el.is(':checkbox')) {
              personal_data[name] = $el.is(':checked') ? $el.val() || '1' : '0';
              return;
            }
            if ($el.is(':radio')) {
              if ($el.is(':checked')) {
                personal_data[name] = $el.val();
              }
              return;
            }
            personal_data[name] = $el.val();
          });
          var couponId = vm.coupon_applied && vm.coupon && vm.coupon.id ? vm.coupon.id : '';
          var query = new URLSearchParams({
            action: 'stm_lms_purchase',
            nonce: stm_lms_nonces['stm_lms_purchase'],
            payment_code: vm.payment_code,
            personal_data: JSON.stringify(personal_data)
          });
          if (couponId) {
            query.append('coupon_id', couponId);
          }
          if (vm.payment_code === 'stripe') {
            var createPM = vm.stripe.createPaymentMethod({
              type: 'card',
              card: vm.stripe_card
            });
            var createTok = vm.stripe.createToken(vm.stripe_card);
            Promise.allSettled([createPM, createTok]).then(function (_ref) {
              var _ref2 = _slicedToArray(_ref, 2),
                pmRes = _ref2[0],
                tokRes = _ref2[1];
              var formData = new FormData();
              formData.append('action', 'stm_lms_purchase');
              formData.append('nonce', stm_lms_nonces['stm_lms_purchase']);
              formData.append('payment_code', vm.payment_code);
              formData.append('personal_data', JSON.stringify(personal_data || {}));
              if (couponId) {
                formData.append('coupon_id', couponId);
              }
              if (pmRes.status === 'fulfilled' && pmRes.value && pmRes.value.paymentMethod) {
                formData.append('payment_method_id', pmRes.value.paymentMethod.id);
                var last4 = pmRes.value.paymentMethod.card && pmRes.value.paymentMethod.card.last4;
                if (last4) {
                  formData.append('card_last4', last4);
                }
              }
              if (tokRes.status === 'fulfilled' && tokRes.value && tokRes.value.token) {
                formData.append('token_id', tokRes.value.token.id);
              }
              if (!formData.get('payment_method_id') && !formData.get('token_id')) {
                var err = pmRes.value && pmRes.value.error || tokRes.value && tokRes.value.error || {
                  message: 'Invalid card details.'
                };
                return handleError(err);
              }
              vm.$http.post(stm_lms_ajaxurl, formData).then(handleResponse)["catch"](handleError);
            });
          } else {
            vm.$http.get(stm_lms_ajaxurl + '?' + query.toString()).then(handleResponse)["catch"](handleError);
          }
        },
        generateStripe: function generateStripe() {
          var vm = this;
          Vue.nextTick(function () {
            vm.stripe = Stripe(stripe_id);
            var elements = vm.stripe.elements();
            vm.stripe_card = elements.create('card');
            vm.stripe_card.mount('#stm-lms-stripe');
            vm.stripe_card.addEventListener('change', function (event) {
              vm.stripe_complete = event.complete;
            });
          });
        },
        formatCurrency: function formatCurrency(amount) {
          var symbol = stm_lms_checkout_settings.currency_symbol || '$';
          var position = stm_lms_checkout_settings.currency_position || 'left';
          var thousands = stm_lms_checkout_settings.currency_thousands || ',';
          var decimals = stm_lms_checkout_settings.currency_decimals || '.';
          var decimalsNum = parseInt(stm_lms_checkout_settings.decimals_num) || 2;
          var fixedAmount = Number(amount).toFixed(decimalsNum);
          var parts = fixedAmount.split('.');
          var integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands);
          var decimalPart = parts[1] || '';
          var formatted = integerPart;
          if (decimalsNum > 0 && parseInt(decimalPart) > 0) {
            formatted += decimals + decimalPart;
          }
          switch (position) {
            case 'left':
              return symbol + formatted;
            case 'left_space':
              return symbol + ' ' + formatted;
            case 'right':
              return formatted + symbol;
            case 'right_space':
              return formatted + ' ' + symbol;
            default:
              return symbol + formatted;
          }
        }
      },
      watch: {
        payment_code: function payment_code(value) {
          if (value === 'stripe') {
            this.generateStripe();
          }
        }
      },
      mounted: function mounted() {
        var gdpr = $('.masterstudy-checkout-gdpr');
        if (!gdpr.length) {
          this.agree_with_policy = true;
        }
        var vm = this;
        var $subtotalEl = jQuery('#subtotal');
        var originalSubtotal = parseFloat($subtotalEl.data('subtotal')) || 0;
        $subtotalEl.data('subtotal-original', originalSubtotal);
        var currentTaxRate = 0;
        var $couponToggle = jQuery('#masterstudy-checkout-coupon-toggle');
        var $couponForm = jQuery('#masterstudy-checkout-coupon-form');
        var $couponInput = jQuery('#masterstudy-checkout-coupon-input');
        var $couponApply = jQuery('#masterstudy-checkout-coupon-apply');
        var $couponRemove = jQuery('#masterstudy-checkout-coupon-remove');
        var $couponMessage = jQuery('#masterstudy-checkout-coupon-message');
        var $couponMessageWrapper = jQuery('.masterstudy-checkout-coupon__message');
        var $couponInputWrapper = jQuery('.masterstudy-checkout-coupon__input');
        var $couponRow = jQuery('#coupon').closest('.masterstudy-checkout-course-info__block');
        var $couponValue = jQuery('#coupon .masterstudy-checkout-course-info__price-value');
        function setCouponMessage(msg, isError) {
          if (!$couponMessage.length) return;
          if (!msg) {
            $couponMessage.text('');
            if ($couponMessageWrapper.length) {
              $couponMessageWrapper.removeClass('masterstudy-checkout-coupon__message_error').hide();
            }
            return;
          }
          $couponMessage.text(msg);
          if ($couponMessageWrapper.length) {
            if (isError) {
              $couponMessageWrapper.addClass('masterstudy-checkout-coupon__message_error');
            } else {
              $couponMessageWrapper.removeClass('masterstudy-checkout-coupon__message_error');
            }
            $couponMessageWrapper.show();
          }
        }
        function setCouponAppliedClass(applied) {
          if (!$couponInputWrapper.length) return;
          $couponInputWrapper.toggleClass('masterstudy-checkout-coupon__input_applied', !!applied);
        }
        function taxFromNetMinor(netMinor, r, taxesEnabled) {
          if (!taxesEnabled || r <= 0) return 0;
          return Math.round(netMinor * r / 100 + Number.EPSILON);
        }
        function recalcByRate(rate) {
          var r = parseFloat(rate) || 0;
          currentTaxRate = r;
          var settings = window.stm_lms_checkout_settings || {};
          var taxesEnabled = !!settings.tax_enabled;
          var taxIncluded = !!settings.tax_included;
          var $subtotalNode = jQuery('#subtotal');
          var isTrial = String($subtotalNode.data('trial')) === '1';
          var $taxesBox = jQuery('#taxes');
          var $taxesBlock = jQuery('#taxes_block');
          var $totalBox = jQuery('#total .masterstudy-checkout-course-info__price-value');
          var $payBtn = jQuery('.stm_lms_pay_button span');
          var $couponRow = jQuery('#coupon').closest('.masterstudy-checkout-course-info__block');
          var $couponVal = jQuery('#coupon .masterstudy-checkout-course-info__price-value');
          var baseSubtotal = parseFloat($subtotalNode.data('subtotal-original')) || parseFloat($subtotalNode.data('subtotal')) || parseFloat(jQuery('#total').data('subtotal')) || 0;
          var decimalsNum = parseInt(stm_lms_checkout_settings.decimals_num) || 2;
          var factor = Math.pow(10, decimalsNum);
          var toMinor = function toMinor(v) {
            return Math.round(Number(v) * factor);
          };
          var fromMinor = function fromMinor(m) {
            return m / factor;
          };
          var F = function F(v) {
            return vm.formatCurrency(v);
          };
          var hasCoupon = vm.coupon_applied && vm.coupon;
          var couponIsActive = hasCoupon && (vm.coupon.is_active === true || vm.coupon.is_active === 1 || vm.coupon.is_active === '1') && !(vm.coupon.is_expired === true || vm.coupon.is_expired === 1 || vm.coupon.is_expired === '1');
          var eligibleSubtotal = 0;
          if (couponIsActive && typeof vm.coupon_applied_subtotal === 'number') {
            eligibleSubtotal = vm.coupon_applied_subtotal;
          }
          var baseSubtotalMinor = toMinor(baseSubtotal);
          baseSubtotal = fromMinor(baseSubtotalMinor);
          var eligibleSubtotalMinor = toMinor(eligibleSubtotal);
          function calcCouponDiscountMinor() {
            if (!couponIsActive) return 0;
            if (eligibleSubtotalMinor <= 0 && !isTrial) return 0;
            var discountValue = parseFloat(vm.coupon.discount) || 0;
            var discountType = (vm.coupon.discount_type || 'percent').toLowerCase();
            if (!(discountValue > 0)) {
              return 0;
            }
            var discountMinor = 0;
            if (discountType === 'percent') {
              discountMinor = Math.round(eligibleSubtotalMinor * discountValue / 100 + Number.EPSILON);
            } else if (discountType === 'amount') {
              discountMinor = toMinor(discountValue);
            }
            if (discountMinor > eligibleSubtotalMinor) {
              discountMinor = eligibleSubtotalMinor;
            }
            return discountMinor;
          }
          function updateCostPriceCurrent(isTrialFlag) {
            var $wrappers = jQuery('.masterstudy-checkout-course-info__cost-price-current');
            if ($wrappers.length) {
              $wrappers.each(function () {
                var $wrap = jQuery(this);
                var base = parseFloat($wrap.data('price-current')) || parseFloat($wrap.data('enrollment-fee')) || 0;
                var baseMinor = toMinor(base);
                var displayMinor = baseMinor;
                if (!isTrialFlag && taxesEnabled) {
                  if (!taxIncluded && r > 0) {
                    var taxMinor = taxFromNetMinor(baseMinor, r, taxesEnabled);
                    displayMinor = baseMinor + taxMinor;
                  }
                }
                var display = fromMinor(displayMinor);
                var $span = $wrap.find('span').first();
                if ($span.length) $span.text(F(display));else $wrap.text(F(display));
              });
            }
            var $saleItems = jQuery('.masterstudy-checkout-course-info__cost-sale-price');
            if ($saleItems.length) {
              $saleItems.each(function () {
                var $sale = jQuery(this);
                var base = parseFloat($sale.data('price-current-sale')) || 0;
                var baseMinor = toMinor(base);
                var displayMinor = baseMinor;
                if (!isTrialFlag && taxesEnabled) {
                  if (!taxIncluded && r > 0) {
                    var taxMinor = taxFromNetMinor(baseMinor, r, taxesEnabled);
                    displayMinor = baseMinor + taxMinor;
                  }
                }
                var display = fromMinor(displayMinor);
                $sale.text(F(display));
              });
            }
            var $items = jQuery('[data-id="checkout-price"]');
            if ($items.length) {
              $items.each(function () {
                var $item = jQuery(this);
                var base = parseFloat($item.data('current-price')) || 0;
                var baseMinor = toMinor(base);
                var displayMinor = baseMinor;
                if (!isTrialFlag && taxesEnabled) {
                  if (!taxIncluded && r > 0) {
                    var taxMinor = taxFromNetMinor(baseMinor, r, taxesEnabled);
                    displayMinor = baseMinor + taxMinor;
                  }
                }
                var display = fromMinor(displayMinor);
                $item.find('span').first().text(F(display));
              });
            }
          }
          function updateTimeline(isTrialFlag) {
            var $items = jQuery('.masterstudy-checkout-course-info__timeline-amount');
            if (!$items.length) return;
            var sumMinor = 0;
            $items.each(function () {
              var $it = jQuery(this);
              var base = parseFloat($it.attr('data-timeline-amount')) || 0;
              var baseMinor = toMinor(base);
              var displayMinor = baseMinor;
              if (!isTrialFlag && taxesEnabled) {
                if (!taxIncluded && r > 0) {
                  var taxMinor = taxFromNetMinor(baseMinor, r, taxesEnabled);
                  displayMinor = baseMinor + taxMinor;
                }
              }
              sumMinor += displayMinor;
              var display = fromMinor(displayMinor);
              $it.text(F(display));
            });
            var $timelineTotal = jQuery('.masterstudy-checkout-course-info__timeline-total strong').first();
            if ($timelineTotal.length) {
              $timelineTotal.text(F(fromMinor(sumMinor)));
            }
          }
          function updateTimelineCoupon(couponDiscount) {
            var $items = jQuery('.masterstudy-checkout-course-info__timeline-amount');
            if (!$items.length) return;
            var firstEl = $items.get(isTrial ? 1 : 0);
            var attrTimelineAmount = $(firstEl).attr('data-timeline-amount');
            var originalAmount = $(firstEl).attr('data-timeline-original-amount');
            var amount = originalAmount ? Number(originalAmount) : Number(attrTimelineAmount || 0);
            if (couponDiscount === 0 || couponDiscount === '0') {
              $(firstEl).attr('data-timeline-amount', originalAmount || attrTimelineAmount);
              return;
            }
            var priceWithDiscount = Math.max(0, amount - couponDiscount);
            $(firstEl).attr('data-timeline-original-amount', amount);
            $(firstEl).attr('data-timeline-amount', priceWithDiscount);
          }
          if (isTrial) {
            if ($taxesBox.length) $taxesBox.text(F(0));
            if ($taxesBlock.length) $taxesBlock.hide();
            if ($totalBox.length) $totalBox.text(F(0));
            if ($payBtn.length) $payBtn.text(F(0));
            var _couponMinor = calcCouponDiscountMinor();
            var _couponAmt = fromMinor(_couponMinor || 0);
            if ($couponRow.length && $couponVal.length) {
              if (_couponMinor > 0) {
                $couponRow.show();
                $couponVal.text('-' + F(_couponAmt));
                updateTimelineCoupon(_couponAmt);
              } else {
                $couponRow.hide();
                $couponVal.text('');
                updateTimelineCoupon(_couponAmt);
              }
            }
            updateCostPriceCurrent(true);
            updateTimeline(true);
            return;
          }
          var netAfterMinor = 0;
          var taxesMinor = 0;
          var totalMinor = 0;
          var couponMinor = calcCouponDiscountMinor();
          if (!taxesEnabled) {
            var netBeforeMinor = baseSubtotalMinor;
            var netAfterMinorLocal = Math.max(0, netBeforeMinor - couponMinor);
            netAfterMinor = netAfterMinorLocal;
            taxesMinor = 0;
            totalMinor = netAfterMinor;
          } else if (!taxIncluded) {
            var _netBeforeMinor = baseSubtotalMinor;
            var _netAfterMinorLocal = Math.max(0, _netBeforeMinor - couponMinor);
            netAfterMinor = _netAfterMinorLocal;
            taxesMinor = taxFromNetMinor(netAfterMinor, r, taxesEnabled);
            totalMinor = netAfterMinor + taxesMinor;
          } else {
            var grossBeforeMinor = baseSubtotalMinor;
            var grossAfterMinor = Math.max(0, grossBeforeMinor - couponMinor);
            var grossAfter = fromMinor(grossAfterMinor);
            var _netAfter = grossAfter;
            if (r > 0) {
              _netAfter = grossAfter * 100 / (100 + r);
            }
            var _taxes = grossAfter - _netAfter;
            netAfterMinor = toMinor(_netAfter);
            taxesMinor = toMinor(_taxes);
            totalMinor = grossAfterMinor;
          }
          var netAfter = fromMinor(netAfterMinor);
          var taxes = fromMinor(taxesMinor);
          var total = fromMinor(totalMinor);
          var couponAmt = fromMinor(couponMinor || 0);
          if ($taxesBox.length) {
            $taxesBox.text(F(taxes));
          }
          if ($taxesBlock.length) {
            if (taxes > 0) {
              $taxesBlock.show();
            } else {
              $taxesBlock.hide();
            }
          }
          if ($totalBox.length) {
            $totalBox.text(F(total));
          }
          if ($payBtn.length) {
            $payBtn.text(F(total));
          }
          if ($couponRow.length && $couponVal.length) {
            if (couponMinor > 0) {
              $couponRow.show();
              $couponVal.text('-' + F(couponAmt));
              updateTimelineCoupon(couponAmt);
            } else {
              $couponRow.hide();
              $couponVal.text('');
              updateTimelineCoupon(couponAmt);
            }
          }
          updateCostPriceCurrent(false);
          updateTimeline(false);
        }
        function getCurrentTaxRate() {
          var settings = window.stm_lms_checkout_settings || {};
          var taxesEnabled = !!settings.tax_enabled;
          if (!taxesEnabled) {
            return 0;
          }
          var taxRates = settings.tax_rates || [];
          if (!taxRates.length) return 0;
          var $country = jQuery('select[name="country"]');
          var $state = jQuery('select[name="state"]');
          var selectedCountry = $country.val();
          if (!selectedCountry) {
            return 0;
          }
          if (selectedCountry === 'US') {
            var selectedState = String($state.val() || '').toUpperCase();
            if (selectedState) {
              var matchState = taxRates.find(function (rate) {
                return rate.country === 'US' && rate.region && rate.region.toUpperCase() === selectedState;
              });
              if (matchState) {
                return parseFloat(matchState.rate) || 0;
              }
            }
            var matchCountry = taxRates.find(function (rate) {
              return rate.country === 'US' && (!rate.region || rate.region === '');
            });
            return matchCountry ? parseFloat(matchCountry.rate) || 0 : 0;
          }
          var matched = taxRates.find(function (rate) {
            return rate.country === selectedCountry && (!rate.region || rate.region === '');
          });
          return matched ? parseFloat(matched.rate) || 0 : 0;
        }
        function processCouponResponse(data, options) {
          options = options || {};
          var silentOnError = !!options.silentOnError;
          var fromInitial = !!options.fromInitial;
          vm.loading = false;
          var status = data.status || '';
          var msg = data.message || '';
          var coupon = data.coupon || null;
          if (!silentOnError) {
            setCouponMessage(msg, status !== 'success');
          } else {
            setCouponMessage('', false);
          }
          if (status !== 'success' || !coupon) {
            if (!silentOnError) {
              vm.status = 'error';
            }
            vm.coupon_applied = false;
            vm.coupon = null;
            vm.coupon_applied_item_ids = [];
            vm.coupon_applied_subtotal = 0;
            setCouponAppliedClass(false);
            if ($couponApply.length && $couponRemove.length) {
              $couponApply.show();
              $couponRemove.hide();
            }
            if (fromInitial) {
              if ($couponForm.length) {
                $couponForm.hide();
              }
              if ($couponToggle.length) {
                $couponToggle.show();
              }
              document.cookie = 'masterstudy_cart_coupon=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';
            }
            recalcByRate(getCurrentTaxRate());
            return;
          }
          vm.status = 'success';
          vm.coupon = coupon;
          vm.coupon_applied = true;
          vm.coupon_applied_item_ids = Array.isArray(data.applied_item_ids) ? data.applied_item_ids : [];
          vm.coupon_applied_subtotal = typeof data.applied_subtotal === 'number' ? data.applied_subtotal : 0;
          setCouponAppliedClass(true);
          if ($couponForm.length) {
            $couponForm.show();
          }
          if ($couponToggle.length) {
            $couponToggle.hide();
          }
          if ($couponApply.length && $couponRemove.length) {
            $couponApply.hide();
            $couponRemove.show();
          }
          setCouponMessage(msg, false);
          recalcByRate(getCurrentTaxRate());
        }
        if ($couponToggle.length && $couponForm.length) {
          $couponToggle.on('click', function (e) {
            e.preventDefault();
            $couponForm.show();
            $couponToggle.hide();
          });
        }
        if ($couponApply.length && $couponInput.length) {
          $couponApply.on('click', function (e) {
            e.preventDefault();
            var code = ($couponInput.val() || '').toString().trim();
            vm.loading = true;
            vm.status = 'pending';
            vm.messages = [];
            setCouponMessage('');
            var apiUrl = "".concat(ms_lms_resturl, "/coupon/apply-cart-coupon");
            fetch(apiUrl, {
              method: 'POST',
              headers: {
                'X-WP-Nonce': ms_lms_nonce,
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({
                code: code
              })
            }).then(function (res) {
              return res.json();
            }).then(function (data) {
              processCouponResponse(data, {
                silentOnError: false,
                fromInitial: false
              });
            })["catch"](function () {
              vm.loading = false;
              vm.status = 'error';
            });
          });
        }
        if ($couponRemove.length) {
          $couponRemove.on('click', function (e) {
            e.preventDefault();
            vm.loading = true;
            vm.status = 'pending';
            vm.messages = [];
            setCouponMessage('');
            var apiUrl = "".concat(ms_lms_resturl, "/coupon/remove-cart-coupon");
            fetch(apiUrl, {
              method: 'POST',
              headers: {
                'X-WP-Nonce': ms_lms_nonce,
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({})
            }).then(function (res) {
              return res.json();
            }).then(function (data) {
              vm.loading = false;
              var status = data.status || '';
              setCouponMessage('');
              if (status !== 'success') {
                vm.status = 'error';
                return;
              }
              vm.status = 'success';
              vm.coupon_applied = false;
              vm.coupon = null;
              vm.coupon_applied_item_ids = [];
              vm.coupon_applied_subtotal = 0;
              setCouponAppliedClass(false);
              $couponInput.val('');
              if ($couponApply.length && $couponRemove.length) {
                $couponApply.show();
                $couponRemove.hide();
              }
              if ($couponForm.length) {
                $couponForm.hide();
              }
              if ($couponToggle.length) {
                $couponToggle.show();
              }
              recalcByRate(getCurrentTaxRate());
            })["catch"](function () {
              vm.loading = false;
              vm.status = 'error';
            });
          });
        }
        function getCookieValue(name) {
          var value = "; ".concat(document.cookie);
          var parts = value.split("; ".concat(name, "="));
          if (parts.length === 2) {
            return decodeURIComponent(parts.pop().split(';').shift());
          }
          return '';
        }
        var initialCodeRaw = getCookieValue('masterstudy_cart_coupon');
        var initialCode = initialCodeRaw ? String(initialCodeRaw).trim() : '';
        if (initialCode) {
          if ($couponInput.length) {
            $couponInput.val(initialCode);
          }
          vm.loading = true;
          vm.status = 'pending';
          vm.messages = [];
          setCouponMessage('');
          var apiUrl = "".concat(ms_lms_resturl, "/coupon/apply-cart-coupon");
          fetch(apiUrl, {
            method: 'POST',
            headers: {
              'X-WP-Nonce': ms_lms_nonce,
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              code: initialCode
            })
          }).then(function (res) {
            return res.json();
          }).then(function (data) {
            processCouponResponse(data, {
              silentOnError: true,
              fromInitial: true
            });
          })["catch"](function () {
            vm.loading = false;
            vm.status = 'error';
          });
        }
        jQuery(document).on('change', 'select[name="country"]', function () {
          recalcByRate(getCurrentTaxRate());
        });
        jQuery(document).on('change', 'select[name="state"]', function () {
          recalcByRate(getCurrentTaxRate());
        });
        recalcByRate(getCurrentTaxRate());
      }
    });
  });
})(jQuery);