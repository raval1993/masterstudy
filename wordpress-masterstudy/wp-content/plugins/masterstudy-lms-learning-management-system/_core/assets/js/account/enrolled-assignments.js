"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, defineProperty = Object.defineProperty || function (obj, key, desc) { obj[key] = desc.value; }, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return defineProperty(generator, "_invoke", { value: makeInvokeMethod(innerFn, self, context) }), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; defineProperty(this, "_invoke", { value: function value(method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; } function maybeInvokeDelegate(delegate, context) { var methodName = context.method, method = delegate.iterator[methodName]; if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel; var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), defineProperty(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (val) { var object = Object(val), keys = []; for (var key in object) keys.push(key); return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
(function ($) {
  $(document).ready(function () {
    $('body').addClass('enrolled-assignments');
    var searchInput = $('[name="masterstudy-account-enrolled-assignments__header-search-input"]');
    var assignmentsGrid = $('.masterstudy-account-enrolled-assignments__items');
    var assignmentsLoader = assignmentsGrid.find('.masterstudy-loader').first();
    var assignmentItemTemplate = document.querySelector('#masterstudy-account-enrolled-assignments__item');
    var paginationContainer = $('.masterstudy-account-enrolled-assignments__pagination');
    var navigationContainer = $('.masterstudy-account-enrolled-assignments__navigation');
    var perPageSelectInput = $('#masterstudy-account-enrolled-assignments__per-page-select');
    var loading = false;
    var currentPage = 1;
    var currentPerPage = Number(perPageSelectInput.val()) || 10;
    var search = '';
    var status = '';
    var assignments = [];
    var totalPages = 0;
    var searchTimeout = 0;
    function updateNavigationAlignment() {
      navigationContainer.css('justify-content', totalPages > 1 ? '' : 'flex-end');
    }
    function setIsLoading(val) {
      loading = val;
      assignmentsLoader.css('display', val ? 'flex' : 'none');
      if (val) {
        paginationContainer.find('.masterstudy-pagination').remove();
      }
      assignmentsGrid.find('.masterstudy-account-enrolled-assignments__item, .masterstudy-account-enrolled-assignments-no-found__info').css('display', val ? 'none' : '');
    }
    function updatePagination(pagination) {
      window._masterstudy_utils.pagination.renderPagination({
        paginationHtml: pagination,
        paginationContainer: paginationContainer,
        totalPages: totalPages,
        currentPage: currentPage,
        onPageChange: function onPageChange(_, page) {
          return getAssignments(currentPerPage, page);
        },
        getPerPageSelector: function getPerPageSelector() {
          return '#masterstudy-account-enrolled-assignments__per-page-select';
        }
      });
    }
    function formatGradeNumber(value) {
      var numericValue = Number(value);
      return Number.isFinite(numericValue) ? numericValue.toFixed(2) : '0.00';
    }
    function renderAssignments() {
      assignmentsGrid.find('.masterstudy-account-enrolled-assignments__item, .masterstudy-account-enrolled-assignments-no-found__info').remove();
      assignments.forEach(function (assignment) {
        var clone = assignmentItemTemplate.content.cloneNode(true);
        var itemClass = '.masterstudy-account-enrolled-assignments__item';
        clone.querySelector(itemClass).href = assignment.url;
        clone.querySelector("".concat(itemClass, "-title")).textContent = assignment.assignment_title;
        clone.querySelector("".concat(itemClass, "-course-value")).textContent = assignment.course_title;
        clone.querySelector("".concat(itemClass, "-teacher-img")).src = assignment.instructor.avatar_url;
        clone.querySelector("".concat(itemClass, "-teacher-info-value")).textContent = assignment.instructor.login;
        clone.querySelector("".concat(itemClass, "-last-update-value")).textContent = assignment.updated_at;
        clone.querySelector("".concat(itemClass, "-status-value")).textContent = assignment.status.label;
        if (assignment.grade) {
          var _assignment$grade$max;
          var gradeScore = clone.querySelector("".concat(itemClass, "-grade-score"));
          gradeScore.style.backgroundColor = assignment.grade.color;
          gradeScore.textContent = assignment.grade.grade;
          clone.querySelector("".concat(itemClass, "-grade-value")).textContent = "(".concat(formatGradeNumber(assignment.grade.point), "/").concat(formatGradeNumber((_assignment$grade$max = assignment.grade.max) === null || _assignment$grade$max === void 0 ? void 0 : _assignment$grade$max.point), ")");
          clone.querySelector("".concat(itemClass, "-grade-percent-value")).textContent = "".concat(assignment.grade.percent, "%");
        } else {
          var _clone$querySelector;
          (_clone$querySelector = clone.querySelector("".concat(itemClass, "-grade"))) === null || _clone$querySelector === void 0 ? void 0 : _clone$querySelector.remove();
        }
        assignmentsGrid.append(clone);
      });
    }
    function getAssignments() {
      return _getAssignments.apply(this, arguments);
    }
    function _getAssignments() {
      _getAssignments = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
        var _status, _search;
        var perPage,
          page,
          params,
          url,
          _assignments$0$pages,
          _assignments$,
          res,
          response,
          hasItems,
          renderNoAssignments,
          _args = arguments;
        return _regeneratorRuntime().wrap(function _callee$(_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              renderNoAssignments = function _renderNoAssignments() {
                var template = document.getElementById('masterstudy-account-enrolled-assignments-no-found-template');
                if (!template) return;
                var clone = template.content.cloneNode(true);
                navigationContainer.hide();
                $(!!(search || status) ? '.masterstudy-no-records__no-items' : '.masterstudy-no-records__no-search', clone).remove();
                assignmentsGrid.find('.masterstudy-account-enrolled-assignments__item, .masterstudy-account-enrolled-assignments-no-found__info').remove();
                assignmentsGrid.append(clone);
              };
              perPage = _args.length > 0 && _args[0] !== undefined ? _args[0] : currentPerPage;
              page = _args.length > 1 && _args[1] !== undefined ? _args[1] : 1;
              if (!loading) {
                _context.next = 5;
                break;
              }
              return _context.abrupt("return");
            case 5:
              currentPage = page;
              currentPerPage = perPage || 10;
              setIsLoading(true);
              assignmentsGrid.find('.masterstudy-account-enrolled-assignments-no-found__info').remove();
              params = new URLSearchParams({
                action: 'stm_lms_get_enrolled_assignments',
                nonce: stm_lms_nonces['stm_lms_get_enrolled_assingments'],
                page: String(currentPage),
                per_page: String(currentPerPage),
                status: String((_status = status) !== null && _status !== void 0 ? _status : ''),
                s: String((_search = search) !== null && _search !== void 0 ? _search : '')
              });
              url = "".concat(stm_lms_ajaxurl, "?").concat(params.toString());
              _context.prev = 11;
              _context.next = 14;
              return fetch(url, {
                method: 'GET'
              });
            case 14:
              res = _context.sent;
              if (res.ok) {
                _context.next = 17;
                break;
              }
              throw new Error("Request failed: ".concat(res.status));
            case 17:
              _context.next = 19;
              return res.json();
            case 19:
              response = _context.sent;
              if (Array.isArray(response.assignments)) {
                _context.next = 22;
                break;
              }
              return _context.abrupt("return");
            case 22:
              assignments = response.assignments;
              hasItems = assignments.length > 0;
              if (hasItems) {
                _context.next = 29;
                break;
              }
              navigationContainer.css('justify-content', '');
              navigationContainer.hide();
              renderNoAssignments();
              return _context.abrupt("return");
            case 29:
              navigationContainer.show();
              totalPages = (_assignments$0$pages = (_assignments$ = assignments[0]) === null || _assignments$ === void 0 ? void 0 : _assignments$.pages) !== null && _assignments$0$pages !== void 0 ? _assignments$0$pages : 1;
              updatePagination(response.pagination);
              updateNavigationAlignment();
              renderAssignments();
              _context.next = 39;
              break;
            case 36:
              _context.prev = 36;
              _context.t0 = _context["catch"](11);
              console.error(_context.t0);
            case 39:
              _context.prev = 39;
              setIsLoading(false);
              return _context.finish(39);
            case 42:
            case "end":
              return _context.stop();
          }
        }, _callee, null, [[11, 36, 39, 42]]);
      }));
      return _getAssignments.apply(this, arguments);
    }
    function init() {
      getAssignments(currentPerPage, 1);
    }
    init();
    document.addEventListener('msfieldEvent', function (e) {
      var _e$detail, _e$detail2;
      if (((_e$detail = e.detail) === null || _e$detail === void 0 ? void 0 : _e$detail.name) === 'masterstudy-account-enrolled-assignments__header-status-select') {
        status = e.detail.value;
      } else if (((_e$detail2 = e.detail) === null || _e$detail2 === void 0 ? void 0 : _e$detail2.name) === 'masterstudy-account-enrolled-assignments__per-page-select') {
        currentPerPage = Number(e.detail.value);
      }
      getAssignments(currentPerPage, 1);
    });
    searchInput.on('input', function () {
      search = $(this).val();
    });
    $('.masterstudy-search__clear-icon').on('click', function () {
      search = '';
      getAssignments(currentPerPage, 1);
    });
    searchInput.on('keyup', function () {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(function () {
        getAssignments(currentPerPage, 1);
      }, 500);
    });
  });
})(jQuery);