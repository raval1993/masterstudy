"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, defineProperty = Object.defineProperty || function (obj, key, desc) { obj[key] = desc.value; }, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return defineProperty(generator, "_invoke", { value: makeInvokeMethod(innerFn, self, context) }), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; defineProperty(this, "_invoke", { value: function value(method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; } function maybeInvokeDelegate(delegate, context) { var methodName = context.method, method = delegate.iterator[methodName]; if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel; var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), defineProperty(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (val) { var object = Object(val), keys = []; for (var key in object) keys.push(key); return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
(function ($) {
  $(document).ready(function () {
    perPageOrders();
    fetchOrders();
  });
  var getTranslatedPaymentCode = function getTranslatedPaymentCode(paymentCode) {
    switch (paymentCode) {
      case 'wire_transfer':
        return masterstudy_orders.payment_code_wire_transfer;
      case 'cash':
        return masterstudy_orders.payment_code_cash;
      case 'mollie':
        return masterstudy_orders.payment_code_mollie;
      case 'paystack':
        return masterstudy_orders.payment_code_paystack;
      default:
        return paymentCode;
    }
  };

  // Build timeline HTML like on thank you page
  function buildOrderTimeline(order, item) {
    try {
      var plan = order && order.plan ? order.plan : null;
      if (!plan || !plan.billing_cycles) return "";
      var billingCycles = parseInt(plan.billing_cycles, 10);
      if (!billingCycles || billingCycles <= 0) return "";

      // Check if trial period exists
      var isTrial = plan.trial_period && parseInt(plan.trial_period, 10) > 0;
      var trialPeriodDays = isTrial ? parseInt(plan.trial_period, 10) : 0;
      var i18n = order && order.i18n ? order.i18n : {};
      var labelPaymentPlan = i18n.payment_plan || 'Payment Plan:';
      var labelDaily = i18n.daily_payments || 'daily payments';
      var labelWeekly = i18n.weekly_payments || 'weekly payments';
      var labelMonthly = i18n.monthly_payments || 'monthly payments';
      var labelYearly = i18n.yearly_payments || 'yearly payments';
      var labelPayment = i18n.payment || 'payment';
      var labelTotal = i18n.total || 'Total:';
      var labelTrial = i18n.trial || 'Trial';
      var labelDays = i18n.days;
      var recurringInterval = plan.recurring_interval || 'month';
      var allowedIntervals = ['day', 'week', 'month', 'year'];

      // Validate interval and default to month if invalid
      if (!allowedIntervals.includes(recurringInterval)) {
        recurringInterval = 'month';
      }
      var recurringIntervalText = recurringInterval === 'year' ? labelYearly : recurringInterval === 'week' ? labelWeekly : recurringInterval === 'day' ? labelDaily : labelMonthly;

      // prefer subscription start date if present
      var startDateStr = order && order.subscription && order.subscription.start_date ? order.subscription.start_date : null;
      var startDate = startDateStr ? new Date(startDateStr) : new Date();

      // Use formatted item price per cycle if available
      var perCycleAmount = item && item.price_with_taxes_formatted ? item.price_with_taxes_formatted : order && order.total_with_taxes ? order.total_with_taxes : '';
      if (isTrial) {
        billingCycles = ++billingCycles;
      }
      var stepsHtml = '';
      for (var i = 1; i <= billingCycles; i++) {
        var _order$locale;
        var stepDate = new Date(startDate.getTime());

        // Calculate date based on trial period
        if (isTrial) {
          if (i === 1) {
            // Trial period starts immediately
            // stepDate remains as startDate
          } else {
            // Subsequent payments start after trial period + intervals
            var intervalsAfterTrial = i - 2; // -2 because first payment is trial, second starts after trial
            if (recurringInterval === 'year') {
              stepDate.setFullYear(stepDate.getFullYear() + intervalsAfterTrial);
            } else if (recurringInterval === 'month') {
              stepDate.setMonth(stepDate.getMonth() + intervalsAfterTrial);
            } else if (recurringInterval === 'week') {
              stepDate.setDate(stepDate.getDate() + intervalsAfterTrial * 7);
            } else if (recurringInterval === 'day') {
              stepDate.setDate(stepDate.getDate() + intervalsAfterTrial);
            }
            // Add trial period days
            stepDate.setDate(stepDate.getDate() + trialPeriodDays);
          }
        } else {
          // No trial - regular intervals
          if (recurringInterval === 'year') {
            stepDate.setFullYear(stepDate.getFullYear() + (i - 1));
          } else if (recurringInterval === 'month') {
            stepDate.setMonth(stepDate.getMonth() + (i - 1));
          } else if (recurringInterval === 'week') {
            stepDate.setDate(stepDate.getDate() + (i - 1) * 7);
          } else if (recurringInterval === 'day') {
            stepDate.setDate(stepDate.getDate() + (i - 1));
          }
        }
        var locale = ((_order$locale = order.locale) === null || _order$locale === void 0 ? void 0 : _order$locale.replace('_', '-')) || undefined;
        var subscription_order_count = order.subscription_order_count || 1;
        var dateFormatter = new Intl.DateTimeFormat(locale, {
          day: '2-digit',
          month: 'long',
          year: 'numeric'
        });
        var formattedDate = dateFormatter.format(stepDate);
        var isChecked = i <= subscription_order_count ? 'checked' : '';
        var isActive = i === subscription_order_count + 1 ? 'active' : '';

        // Determine step title and amount based on trial
        var stepTitle = void 0,
          stepAmount = void 0;
        if (isTrial && i === 1) {
          stepTitle = "".concat(labelTrial, " ").concat(trialPeriodDays, " ").concat(labelDays);
          stepAmount = '$0'; // Trial is free
        } else {
          stepTitle = "".concat(i, " ").concat(labelPayment);
          stepAmount = perCycleAmount;
        }
        if (order.coupon_id || order.first_order_coupon) {
          var firstPaymentIdx = 1;
          if (isTrial) {
            ++firstPaymentIdx;
          }
          if (i === firstPaymentIdx) {
            var _order$coupon_item_pr, _order$first_order_co;
            stepAmount = (_order$coupon_item_pr = order.coupon_item_price_formatted) !== null && _order$coupon_item_pr !== void 0 ? _order$coupon_item_pr : (_order$first_order_co = order.first_order_coupon) === null || _order$first_order_co === void 0 ? void 0 : _order$first_order_co.coupon_item_price_formatted;
          }
        }
        stepsHtml += "\n          <div class=\"masterstudy-orders-course-info__timeline-step ".concat(isChecked).concat(isActive ? ' ' + isActive : '', "\">\n            <div class=\"masterstudy-orders-course-info__timeline-circle\"></div>\n            <div class=\"masterstudy-orders-course-info__timeline-content\">\n              <span class=\"masterstudy-orders-course-info__timeline-title\">").concat(stepTitle, "</span>\n              <span class=\"masterstudy-orders-course-info__timeline-date\">").concat(formattedDate, "</span>\n              <span class=\"masterstudy-orders-course-info__timeline-amount\">").concat(stepAmount, "</span>\n            </div>\n          </div>");
      }

      // Compute total amount across billing cycles if numeric price is available
      var totalBlockHtml = '';
      var unitPriceNumeric = item && typeof item.price !== 'undefined' ? parseFloat(item.price_with_taxes) : NaN;
      if (!Number.isNaN(unitPriceNumeric) && Number.isFinite(unitPriceNumeric)) {
        // Calculate total considering trial period
        var totalNumeric;
        if (isTrial) {
          // Trial is free, so total is (cycles - 1) * price
          totalNumeric = unitPriceNumeric * (billingCycles - 1);
        } else {
          // No trial - regular calculation
          totalNumeric = unitPriceNumeric * billingCycles;
        }

        // If we have coupon in subscription, then remove the discount from total amount
        if (order.coupon_id && !Number.isNaN(Number(order.coupon_item_discount))) {
          totalNumeric -= Math.min(unitPriceNumeric, Number(order.coupon_item_discount));
        } else if (order.first_order_coupon && !Number.isNaN(order.first_order_coupon.coupon_item_discount)) {
          totalNumeric -= Math.min(unitPriceNumeric, Number(order.first_order_coupon.coupon_item_discount));
        }

        // Try to mimic formatting from perCycleAmount by detecting currency symbol placement
        var example = perCycleAmount || '';
        var prefix = '';
        var suffix = '';
        // Extract non-numeric prefix/suffix as currency symbol(s)
        var leadingSymbols = example.match(/^\s*([^0-9.,\s]+)/);
        var trailingSymbols = example.match(/([^0-9.,\s]+)\s*$/);
        if (leadingSymbols && leadingSymbols[1]) prefix = leadingSymbols[1];
        if (trailingSymbols && trailingSymbols[1]) suffix = trailingSymbols[1];
        var formattedNumber = new Intl.NumberFormat(undefined, {
          minimumFractionDigits: 0,
          maximumFractionDigits: 2
        }).format(totalNumeric);
        var totalFormatted = "".concat(prefix).concat(prefix ? ' ' : '').concat(formattedNumber).concat(suffix ? ' ' : '').concat(suffix).trim();
        totalBlockHtml = "\n          <div class=\"masterstudy-orders-course-info__timeline-total\">\n            <span>".concat(labelTotal, "</span>\n            <strong>").concat(totalFormatted, "</strong>\n          </div>");
      }
      return "\n        <div class=\"masterstudy-orders-course-info__timeline\">\n          <div class=\"masterstudy-orders-course-info__timeline-plan-title\">\n            ".concat(labelPaymentPlan, "\n            <span>").concat(billingCycles, " ").concat(recurringIntervalText, "</span>\n          </div>\n          ").concat(stepsHtml, "\n        </div>\n        ").concat(totalBlockHtml);
    } catch (e) {
      return "";
    }
  }
  var getStatusName = function getStatusName(status) {
    switch (status) {
      case 'completed':
        return masterstudy_orders.statuses.completed;
      case 'cancelled':
        return masterstudy_orders.statuses.cancelled;
      case 'pending':
        return masterstudy_orders.statuses.pending;
      default:
        return status;
    }
  };

  //Function to retrieve data via API
  function fetchOrders(_x) {
    return _fetchOrders.apply(this, arguments);
  } //Function to update the page count request
  function _fetchOrders() {
    _fetchOrders = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee(perPage) {
      var currentPage,
        apiUrl,
        queryParams,
        ordersContainer,
        response,
        data,
        orders,
        orderHtml,
        _args = arguments;
      return _regeneratorRuntime().wrap(function _callee$(_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            currentPage = _args.length > 1 && _args[1] !== undefined ? _args[1] : 1;
            apiUrl = typeof masterstudy_woocommerce_orders !== "undefined" ? "".concat(ms_lms_resturl, "/orders/woocommerce-orders") : "".concat(ms_lms_resturl, "/orders");
            queryParams = [];
            if (perPage !== undefined) {
              queryParams.push("per_page=".concat(perPage));
            }
            if (currentPage !== undefined) {
              queryParams.push("current_page=".concat(currentPage));
            }
            if (queryParams.length > 0) {
              apiUrl += '?' + queryParams.join('&');
            }

            //Add animation for container
            ordersContainer = $(".masterstudy-orders-container");
            ordersContainer.addClass("orders-loading");
            _context.prev = 8;
            _context.next = 11;
            return fetch(apiUrl, {
              method: "GET",
              headers: {
                "X-WP-Nonce": ms_lms_nonce,
                "Content-Type": "application/json"
              }
            });
          case 11:
            response = _context.sent;
            if (response.ok) {
              _context.next = 14;
              break;
            }
            throw new Error("HTTP error! status: ".concat(response.status));
          case 14:
            _context.next = 16;
            return response.json();
          case 16:
            data = _context.sent;
            //Remove animation for container
            ordersContainer.css("height", "auto").removeClass("orders-loading");
            $(".masterstudy-orders .stm_lms_user_info_top h3").html(function (_, currentHtml) {
              return currentHtml.replace(/<span>.*<\/span>/, "") + " <span>" + data.total_orders + "</span>";
            });

            //Update pagination data
            updatePagination(data.pages, currentPage);
            orders = data.orders;
            if (orders && orders.length > 0) {
              if (Array.isArray(orders)) {
                orders.forEach(function (order) {
                  var template = document.getElementById("masterstudy-order-template");
                  var clone = template.content.cloneNode(true);
                  $(clone).find("[data-order-id]").text("#".concat(order.id));
                  $(clone).find("[data-order-status]").text(getStatusName(order.status_name || order.status)).addClass("".concat(order.status));
                  $(clone).find("[data-order-date]").text("".concat(order.date_formatted));
                  $(clone).find("[data-order-payment]").text(getTranslatedPaymentCode(order.payment_code));
                  var _loop = function _loop() {
                    if (order.cart_items.hasOwnProperty(key)) {
                      var item = order.cart_items[key];
                      var matchingItem = order.items.find(function (i) {
                        return Number(i.item_id) === Number(item.item_id) || Number(i.enterprise_id) === Number(item.enterprise_id);
                      });
                      var additionalInfo = "";
                      if (matchingItem) {
                        if (matchingItem.enterprise && matchingItem.enterprise !== "0" || matchingItem.enterprise_id && matchingItem.enterprise_id !== "0") {
                          additionalInfo = "<span class=\"order-status\">".concat(masterstudy_orders.enterprise, "</span>");
                        } else if (matchingItem.bundle && matchingItem.bundle !== "0" || item.bundle_courses_count > 0) {
                          additionalInfo = "<span class=\"order-status\">".concat(masterstudy_orders.bundle, "</span>");
                        } else if (order.is_subscription) {
                          additionalInfo = "<span class=\"order-status\">".concat(masterstudy_orders.subscription, "</span>");
                        }
                      }
                      var hasBillingCycles = order && order.plan && Number(order.plan.billing_cycles) > 0;
                      var isSubscription = order.is_subscription ? order.is_subscription : false;
                      var timelineHtml = buildOrderTimeline(order, item);
                      // Use course_info for subscriptions if available, otherwise use cart_items data
                      var displayImage = item.image;
                      var displayTitle = item.title;
                      var displayLink = item.link;
                      if (isSubscription) {
                        var course_info = order.course_info,
                          plan = order.plan;
                        if (course_info && 'course' === plan.type) {
                          displayImage = course_info.course_thumbnail ? "<img src=\"".concat(course_info.course_thumbnail, "\" alt=\"").concat(course_info.course_title || 'Course', "\" />") : item.image;
                          displayTitle = course_info.course_title || plan.name;
                          displayLink = course_info.course_url || item.link;
                        } else {
                          displayTitle = plan.name;
                          displayLink = plan.memberships_url || '/';
                        }
                      }
                      var orderHtml = "\n              <div class=\"masterstudy-orders-table__body-row".concat(isSubscription ? ' memberships' : '', "\">\n                <div class=\"masterstudy-orders-course-info").concat(hasBillingCycles ? ' billing-cycles' : '', "\">\n                  <div class=\"masterstudy-orders-course-info__image\">").concat(displayImage ? "<a href=\"".concat(displayLink, "\">").concat(displayImage, "</a>") : isSubscription ? '' : "<img src=\"".concat(item.placeholder, "\" alt=\"").concat(displayTitle, "\">"), "</div>\n                  <div class=\"masterstudy-orders-course-info__common\">\n                    <div class=\"masterstudy-orders-course-info__title\">").concat(displayTitle ? "<a href=\"".concat(displayLink, "\">").concat(displayTitle, "</a>") : "<em>N/A</em>", " ").concat(additionalInfo, "</div>\n                    <div class=\"masterstudy-orders-course-info__category\">\n                    ").concat(item.enterprise_name ? "".concat(order.i18n.enterprise, " ").concat(item.enterprise_name) : " ".concat(item.terms.join(", ")), "\n                    ").concat(item.bundle_courses_count > 0 ? "".concat(item.bundle_courses_count, " ").concat(order.i18n.bundle) : "", "\n                    </div>\n                    ").concat(timelineHtml, "\n                  </div>\n                  <div class=\"masterstudy-orders-course-info__price\">").concat(item.price_formatted, "</div>\n                </div>\n              </div>");
                      $(clone).find(".masterstudy-orders-table__body").append(orderHtml);
                    }
                  };
                  for (var key in order.cart_items) {
                    _loop();
                  }
                  $(clone).find("[data-order-total]").text("".concat(order.total_formatted));
                  $(clone).find("[data-order-subtotal]").text("".concat(order.subtotal_formatted));
                  $(clone).find("[data-order-taxes]").text("".concat(order.taxes_formatted));
                  if (order.coupon_value) {
                    $(clone).find("[data-order-coupon]").text(order.coupon_value);
                  } else {
                    $(clone).find('[data-id="coupon"]').remove();
                  }
                  var detailsContainer = $(clone).find(".masterstudy-orders-course-info__details");
                  var button = detailsContainer.find(".masterstudy-button");
                  if (button.length > 0) {
                    var baseUrl = "".concat(window.location.origin).concat(window.location.pathname.split('/').slice(0, 2).join('/'));
                    button.attr("href", "".concat(baseUrl, "/woocommerce-order-details/").concat(order.id));
                  }
                  $(".masterstudy-orders-container").append(clone);
                });
              }
            } else {
              orderHtml = "\n              <div class=\"masterstudy-orders-no-found__info\">\n                <div class=\"masterstudy-orders-no-found__info-icon\"><i class=\"stmlms-orders\"></i></div>\n                <div class=\"masterstudy-orders-no-found__info-title\">".concat(masterstudy_orders.no_order_title, "</div>\n                <a href=\"").concat(masterstudy_orders.courses_page, "\" target=\"_blank\" class=\"masterstudy-button masterstudy-button_style-primary masterstudy-button_size-sm\">\n                  <span class=\"masterstudy-button__title\">").concat(masterstudy_orders.button_title, "</span>\n                </a>\n            </div>");
              $(".masterstudy-orders").append("".concat(orderHtml)).addClass("masterstudy-orders-no-found");
            }
            _context.next = 28;
            break;
          case 24:
            _context.prev = 24;
            _context.t0 = _context["catch"](8);
            console.error("Error fetching orders:", _context.t0);
            ordersContainer.css("height", "auto").removeClass("orders-loading");
          case 28:
          case "end":
            return _context.stop();
        }
      }, _callee, null, [[8, 24]]);
    }));
    return _fetchOrders.apply(this, arguments);
  }
  function perPageOrders(perPage) {
    $(".masterstudy-select__option, .masterstudy-select__clear").on("click", function () {
      $(".masterstudy-orders-container .masterstudy-orders-table").remove();
      var perPage = $(this).data("value");
      fetchOrders(perPage);
    });
  }

  //Function to update API data for pagination
  function updatePagination(totalPages, currentPage) {
    var prefix = typeof masterstudy_woocommerce_orders !== "undefined" ? masterstudy_woocommerce_orders : masterstudy_orders;
    $.ajax({
      url: prefix.ajaxurl,
      method: "POST",
      data: {
        action: "get_pagination",
        total_pages: totalPages,
        current_page: currentPage,
        _ajax_nonce: prefix.nonce
      },
      success: function success(response) {
        if (response.success) {
          $(".masterstudy-orders-table-navigation__pagination").toggle(totalPages > 1);
          $(".masterstudy-orders-table-navigation__pagination").html(response.data.pagination);
          attachPaginationClickHandlers(totalPages);
          $(".masterstudy-pagination__button-next").toggleClass("masterstudy-pagination__button_disabled", currentPage >= totalPages);
          $(".masterstudy-pagination__button-prev").toggleClass("masterstudy-pagination__button_disabled", currentPage <= 1);
          updatePaginationView(totalPages, currentPage);
        } else {
          console.error("Error updating pagination:", response.data);
        }
      },
      error: function error(_error) {
        console.error("AJAX error:", _error);
      }
    });
  }

  //Function to update the page
  function attachPaginationClickHandlers(totalPages) {
    $(".masterstudy-pagination__item-block").on("click", function () {
      tableHeight();
      var currentPage = $(this).data("id");
      $(".masterstudy-orders-container .masterstudy-orders-table").remove();
      var perPage = $("#orders-per-page").val();
      updatePagination(totalPages, currentPage);
      fetchOrders(perPage, currentPage);
    });
    $(".masterstudy-pagination__button-prev").on("click", function () {
      tableHeight();
      var currentPageElement = $(".masterstudy-pagination__item_current .masterstudy-pagination__item-block");
      if (currentPageElement.length) {
        var currentPage = parseInt(currentPageElement.data("id"));
        currentPage -= 1;
        if (currentPage >= 1) {
          $(".masterstudy-orders-container .masterstudy-orders-table").remove();
          var perPage = $("#orders-per-page").val();
          updatePagination(totalPages, currentPage);
          fetchOrders(perPage, currentPage);
        }
      }
    });
    $(".masterstudy-pagination__button-next").on("click", function () {
      tableHeight();
      var currentPageElement = $(".masterstudy-pagination__item_current .masterstudy-pagination__item-block");
      if (currentPageElement.length) {
        var currentPage = parseInt(currentPageElement.data("id"));
        currentPage += 1;
        var _totalPages = $(".masterstudy-pagination__item-block").length;
        if (currentPage <= _totalPages) {
          $(".masterstudy-orders-container .masterstudy-orders-table").remove();
          var perPage = $("#orders-per-page").val();
          updatePagination(_totalPages, currentPage);
          fetchOrders(perPage, currentPage);
        }
      }
    });
  }

  //Animation when switching pagination
  function updatePaginationView(totalPages, currentPage) {
    $(".masterstudy-pagination__item").hide();
    var startPage = Math.max(1, currentPage - 1);
    var endPage = Math.min(totalPages, currentPage + 1);
    if (currentPage === 1 || startPage === 1) {
      endPage = Math.min(totalPages, startPage + 2);
    } else if (currentPage === totalPages || endPage === totalPages) {
      startPage = Math.max(1, endPage - 2);
    }
    for (var i = startPage; i <= endPage; i++) {
      $(".masterstudy-pagination__item:has([data-id=\"".concat(i, "\"])")).show();
    }
    $(".masterstudy-pagination__button-next").toggle(currentPage < totalPages);
    $(".masterstudy-pagination__button-prev").toggle(currentPage > 1);
  }

  //Function animation for container
  function tableHeight() {
    var ordersContainer = $(".masterstudy-orders-container");
    var containerHeight = ordersContainer.height();
    ordersContainer.css("height", containerHeight);
    ordersContainer.removeClass("orders-loading");
  }
})(jQuery);