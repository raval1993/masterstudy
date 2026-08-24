"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, defineProperty = Object.defineProperty || function (obj, key, desc) { obj[key] = desc.value; }, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return defineProperty(generator, "_invoke", { value: makeInvokeMethod(innerFn, self, context) }), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; defineProperty(this, "_invoke", { value: function value(method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; } function maybeInvokeDelegate(delegate, context) { var methodName = context.method, method = delegate.iterator[methodName]; if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel; var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), defineProperty(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (val) { var object = Object(val), keys = []; for (var key in object) keys.push(key); return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
function _iterableToArrayLimit(arr, i) { var _i = null == arr ? null : "undefined" != typeof Symbol && arr[Symbol.iterator] || arr["@@iterator"]; if (null != _i) { var _s, _e, _x, _r, _arr = [], _n = !0, _d = !1; try { if (_x = (_i = _i.call(arr)).next, 0 === i) { if (Object(_i) !== _i) return; _n = !1; } else for (; !(_n = (_s = _x.call(_i)).done) && (_arr.push(_s.value), _arr.length !== i); _n = !0); } catch (err) { _d = !0, _e = err; } finally { try { if (!_n && null != _i["return"] && (_r = _i["return"](), Object(_r) !== _r)) return; } finally { if (_d) throw _e; } } return _arr; } }
function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }
(function ($) {
  $(document).ready(function () {
    var chartsContainer = $('.masterstudy-account-statistics__charts');
    var revenueChartWrapper = $('.masterstudy-account-statistics__revenue-chart-wrapper');
    var revenueLoader = $('.masterstudy-account-statistics__revenue-loader');
    var paypalEmailInput = $('.masterstudy-account-statistics__email-input');
    var paypalEmailResponse = $('.masterstudy-account-statistics__paypal-email-response');
    var saveEmailBtn = $('.payout-save-email');
    var totalRows = $('.masterstudy-account-statistics__orders-header-title-total');
    var totalPrice = $('.masterstudy-account-statistics__revenue-total-price');
    var ordersTable;
    var orderColumns = masterstudy_lms_statistics_data.order_columns;
    window.defaultDateRanges = getDefaultDateRanges();
    var selectedKey = localStorage.getItem('StatisticsSelectedPeriodKey');
    var selectedPeriod = localStorage.getItem('StatisticsSelectedPeriod');
    if (selectedKey) {
      window.selectedPeriod = window.defaultDateRanges[selectedKey];
    } else if (selectedPeriod) {
      window.selectedPeriod = JSON.parse(selectedPeriod).map(function (d) {
        return new Date(d);
      });
    } else {
      window.selectedPeriod = window.defaultDateRanges['this_month'];
    }
    window.stats_data = masterstudy_lms_statistics_data.stats_data;
    window.stats_data.is_account_statistics = true;
    var api = new MasterstudyApiProvider('');
    window.api_data.rest_url = masterstudy_lms_statistics_data.rest_url;
    var oldLmsApi = new MasterstudyApiProvider('lms');
    var currencySettings = {
      symbol: '$',
      position: 'left',
      thousands: ',',
      decimals: '.',
      decimalsNum: 2
    };
    var paypalEmail = null;
    var isPaypalEmailLoading = false;
    var labelsEarnings = null;
    var datasetsEarnings = null;
    var dateFrom = null;
    var dateTo = null;
    var selectedCourseId = 0;
    var selectedStatus = '';
    var lineChart = null;
    var revenueChartHeight = 500;
    var revenueLegendMaxHeight = 150;
    var isLegendSizePatched = false;
    function applyLegendMaxSizePatch() {
      var _window$Chart;
      if (isLegendSizePatched || !((_window$Chart = window.Chart) !== null && _window$Chart !== void 0 && (_window$Chart = _window$Chart.Legend) !== null && _window$Chart !== void 0 && _window$Chart.prototype)) {
        return;
      }
      var legendPrototype = window.Chart.Legend.prototype;
      var originalAfterFit = legendPrototype.afterFit;
      legendPrototype.afterFit = function () {
        var _this$options, _this$options2;
        originalAfterFit.call(this);
        if (((_this$options = this.options) === null || _this$options === void 0 || (_this$options = _this$options.maxSize) === null || _this$options === void 0 ? void 0 : _this$options.height) !== undefined) {
          this.height = Math.min(this.height, this.options.maxSize.height);
          this.minSize.height = Math.min(this.minSize.height, this.height);
        }
        if (((_this$options2 = this.options) === null || _this$options2 === void 0 || (_this$options2 = _this$options2.maxSize) === null || _this$options2 === void 0 ? void 0 : _this$options2.width) !== undefined) {
          this.width = Math.min(this.width, this.options.maxSize.width);
          this.minSize.width = Math.min(this.minSize.width, this.width);
        }
      };
      isLegendSizePatched = true;
    }
    function setRevenueChartFixedHeight() {
      var canvas = document.getElementById('line_chart_id');
      var chartContainer = canvas === null || canvas === void 0 ? void 0 : canvas.parentElement;
      if (!chartContainer) {
        return;
      }
      chartContainer.style.height = "".concat(revenueChartHeight, "px");
      chartContainer.style.minHeight = "".concat(revenueChartHeight, "px");
    }
    function setIsPaypalEmailLoading(val) {
      saveEmailBtn.toggleClass('loading', val);
    }
    function setPaypalEmailResponse(msg, status) {
      paypalEmailResponse.attr('class', 'masterstudy-account-statistics__message masterstudy-account-statistics__paypal-email-response masterstudy-account-utility__message');
      paypalEmailResponse.toggleClass('masterstudy-account-utility_hidden', !msg);
      paypalEmailResponse.addClass(status);
      paypalEmailResponse.text(msg);
    }
    function setCurrencySettings() {
      var _data$currency_symbol, _data$currency_positi, _data$currency_thousa, _data$currency_decima, _data$decimals_num;
      var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      currencySettings = {
        symbol: (_data$currency_symbol = data.currency_symbol) !== null && _data$currency_symbol !== void 0 ? _data$currency_symbol : currencySettings.symbol,
        position: (_data$currency_positi = data.currency_position) !== null && _data$currency_positi !== void 0 ? _data$currency_positi : currencySettings.position,
        thousands: (_data$currency_thousa = data.currency_thousands) !== null && _data$currency_thousa !== void 0 ? _data$currency_thousa : currencySettings.thousands,
        decimals: (_data$currency_decima = data.currency_decimals) !== null && _data$currency_decima !== void 0 ? _data$currency_decima : currencySettings.decimals,
        decimalsNum: Number((_data$decimals_num = data.decimals_num) !== null && _data$decimals_num !== void 0 ? _data$decimals_num : currencySettings.decimalsNum)
      };
    }
    function formatPriceNumber(price) {
      var numericPrice = Number(price);
      if (Number.isNaN(numericPrice)) {
        return price;
      }
      var hasFraction = Math.abs(numericPrice % 1) > Number.EPSILON;
      var decimalsNum = hasFraction ? currencySettings.decimalsNum : 0;
      var fixedPrice = numericPrice.toFixed(decimalsNum);
      var _fixedPrice$split = fixedPrice.split('.'),
        _fixedPrice$split2 = _slicedToArray(_fixedPrice$split, 2),
        integerPart = _fixedPrice$split2[0],
        decimalPart = _fixedPrice$split2[1];
      integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, currencySettings.thousands);
      if (decimalPart !== undefined && decimalsNum > 0) {
        return "".concat(integerPart).concat(currencySettings.decimals).concat(decimalPart);
      }
      return integerPart;
    }
    function formatPrice(price) {
      var formattedPrice = formatPriceNumber(price);
      if ('right' === currencySettings.position) {
        return "".concat(formattedPrice).concat(currencySettings.symbol);
      }
      return "".concat(currencySettings.symbol).concat(formattedPrice);
    }
    function createLineChart() {
      applyLegendMaxSizePatch();
      setRevenueChartFixedHeight();
      var ctx = document.getElementById('line_chart_id').getContext('2d');
      lineChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: labelsEarnings,
          datasets: datasetsEarnings
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              type: 'linear',
              beginAtZero: true,
              grid: {
                display: true,
                color: 'rgba(219,224,233,1)',
                borderColor: 'rgba(77,94,111,1)'
              },
              ticks: {
                callback: function callback(tick) {
                  return formatPrice(tick);
                }
              }
            }
          },
          plugins: {
            legend: {
              display: true,
              position: 'bottom',
              maxHeight: revenueLegendMaxHeight,
              maxSize: {
                height: revenueLegendMaxHeight
              },
              labels: {
                boxWidth: 10,
                usePointStyle: true,
                padding: 20
              }
            },
            tooltip: {
              callbacks: {
                label: function label(context) {
                  var _context$dataset, _ref, _context$parsed$y;
                  var label = (_context$dataset = context.dataset) !== null && _context$dataset !== void 0 && _context$dataset.label ? "".concat(context.dataset.label, ": ") : '';
                  return "".concat(label).concat(formatPrice((_ref = (_context$parsed$y = context.parsed.y) !== null && _context$parsed$y !== void 0 ? _context$parsed$y : context.raw) !== null && _ref !== void 0 ? _ref : 0));
                }
              }
            }
          }
        }
      });
    }
    function setRevenueChartLoading(val) {
      revenueLoader.toggleClass('masterstudy-account-utility_hidden', !val);
      revenueChartWrapper.toggleClass('masterstudy-account-utility_hidden', val);
    }
    function renderRevenueChart() {
      return _renderRevenueChart.apply(this, arguments);
    }
    function _renderRevenueChart() {
      _renderRevenueChart = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
        var searchParams, res;
        return _regeneratorRuntime().wrap(function _callee$(_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              _context.prev = 0;
              setRevenueChartLoading(true);
              searchParams = {};
              if (selectedCourseId) {
                searchParams['course_id'] = selectedCourseId;
              }
              if (dateFrom && dateTo) {
                searchParams['date_from'] = dateFrom;
                searchParams['date_to'] = dateTo;
              }
              _context.next = 7;
              return api.get("payout/revenue", searchParams);
            case 7:
              res = _context.sent;
              setCurrencySettings(res);
              labelsEarnings = res.labels_earnings;
              if (res.datasets_earnings) {
                datasetsEarnings = Array.isArray(res.datasets_earnings) ? res.datasets_earnings : Object.values(res.datasets_earnings);
                datasetsEarnings.forEach(function (item) {
                  item.fill = true;
                  item.tension = 0.4;
                });
              }
              if (datasetsEarnings) {
                chartsContainer.removeClass('masterstudy-account-utility_hidden');
              }
              if (!lineChart) {
                createLineChart();
              } else {
                setRevenueChartFixedHeight();
                lineChart.data.datasets = datasetsEarnings;
                lineChart.data.labels = labelsEarnings;
                lineChart.update();
              }
              _context.next = 18;
              break;
            case 15:
              _context.prev = 15;
              _context.t0 = _context["catch"](0);
              console.error(_context.t0);
            case 18:
              _context.prev = 18;
              setRevenueChartLoading(false);
              return _context.finish(18);
            case 21:
            case "end":
              return _context.stop();
          }
        }, _callee, null, [[0, 15, 18, 21]]);
      }));
      return _renderRevenueChart.apply(this, arguments);
    }
    function setDates(range) {
      dateFrom = moment(range[0]).format('YYYY-MM-DD');
      dateTo = moment(range[1]).format('YYYY-MM-DD');
    }
    function updateTable() {
      var reloadTable = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      var dataSrc = function dataSrc(json) {
        totalRows.text(json.recordsTotal);
        totalPrice.text(json.formatted_price);
        json.data = json.data.map(function (item) {
          return item;
        });
        return json.data;
      };
      var columnDefs = buildColumnDefs(orderColumns);
      var searchParams = new URLSearchParams();
      if (dateFrom && dateTo) {
        searchParams.append('date_from', dateFrom);
        searchParams.append('date_to', dateTo);
      }
      if (selectedCourseId) {
        searchParams.append('course_id', selectedCourseId);
      }
      if (selectedStatus) {
        searchParams.append('status', selectedStatus);
      }
      ordersTable = updateDataTable(ordersTable, '#masterstudy-datatable-orders', ['.masterstudy-account-statistics__orders'], "payout/orders?".concat(searchParams.toString()), orderColumns, dataSrc, columnDefs, reloadTable);
    }
    function buildColumnDefs(columns) {
      return columns.map(function (col, index) {
        var def = {
          targets: index,
          data: col.data,
          orderable: ['date_created'].includes(col.data)
        };
        switch (col.data) {
          case 'number':
            def.render = function (_data, _type, row) {
              return row.id;
            };
            break;
          case 'date_created':
            def.render = function (_data, _type, row) {
              return "".concat(row.date_created_formatted.date, ", ").concat(row.date_created_formatted.time);
            };
            break;
          case 'payment_code':
            def.render = function (_data, _type, row) {
              return row.method;
            };
            break;
          case 'status':
            def.render = function (_data, _type, row) {
              return "<div class=\"masterstudy-account-statistics__row-status masterstudy-account-statistics__row-status_".concat(row.status, "\">").concat(row.status, "</div>");
            };
            break;
          case 'amount':
            def.render = function (_data, _type, row) {
              return row.amount_formatted;
            };
            break;
        }
        return def;
      });
    }
    function saveEmail() {
      return _saveEmail.apply(this, arguments);
    }
    function _saveEmail() {
      _saveEmail = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee2() {
        var formData, res;
        return _regeneratorRuntime().wrap(function _callee2$(_context2) {
          while (1) switch (_context2.prev = _context2.next) {
            case 0:
              if (!isPaypalEmailLoading) {
                _context2.next = 2;
                break;
              }
              return _context2.abrupt("return");
            case 2:
              setIsPaypalEmailLoading(true);
              setPaypalEmailResponse('', '');
              formData = new FormData();
              formData.append('paypal_email', paypalEmail);
              _context2.prev = 6;
              _context2.next = 9;
              return oldLmsApi.postFormData('/stm-lms-payout/paypal-email', formData);
            case 9:
              res = _context2.sent;
              setPaypalEmailResponse(res.message, res.status);
              _context2.next = 16;
              break;
            case 13:
              _context2.prev = 13;
              _context2.t0 = _context2["catch"](6);
              console.error(_context2.t0);
            case 16:
              _context2.prev = 16;
              setIsPaypalEmailLoading(false);
              return _context2.finish(16);
            case 19:
            case "end":
              return _context2.stop();
          }
        }, _callee2, null, [[6, 13, 16, 19]]);
      }));
      return _saveEmail.apply(this, arguments);
    }
    function init() {
      return _init.apply(this, arguments);
    }
    function _init() {
      _init = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee3() {
        return _regeneratorRuntime().wrap(function _callee3$(_context3) {
          while (1) switch (_context3.prev = _context3.next) {
            case 0:
              setDates(window.selectedPeriod);
              initializeDatepicker('#masterstudy-datepicker-stm-account-statistics');
              void renderRevenueChart();
              updateTable(true);
            case 4:
            case "end":
              return _context3.stop();
          }
        }, _callee3);
      }));
      return _init.apply(this, arguments);
    }
    void init();
    paypalEmailInput.on('input', function () {
      paypalEmail = $(this).val();
    });
    saveEmailBtn.on('click', function () {
      saveEmail();
    });
    document.addEventListener('msfieldEvent', function (event) {
      if (event.detail.name === 'masterstudy-account-statistics__revenue-select-input') {
        selectedCourseId = event.detail.value;
        updateTable(true);
        void renderRevenueChart();
      } else if (event.detail.name === 'status') {
        selectedStatus = event.detail.value;
        updateTable(true);
      }
    });
    document.addEventListener('datesUpdated', function (e) {
      setDates(e.detail.selectedPeriod);
      updateTable(true);
      void renderRevenueChart();
    });
  });
})(jQuery);