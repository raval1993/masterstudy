"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, defineProperty = Object.defineProperty || function (obj, key, desc) { obj[key] = desc.value; }, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return defineProperty(generator, "_invoke", { value: makeInvokeMethod(innerFn, self, context) }), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; defineProperty(this, "_invoke", { value: function value(method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; } function maybeInvokeDelegate(delegate, context) { var methodName = context.method, method = delegate.iterator[methodName]; if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel; var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), defineProperty(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (val) { var object = Object(val), keys = []; for (var key in object) keys.push(key); return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"]; if (!it) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
(function ($) {
  $(document).ready(function () {
    var events = {
      openCreateDrawer: 'enterprise-groups-open-create-drawer',
      openImportCsvModal: 'enterprise-groups-open-csv-import-modal',
      openViewDrawer: 'enterprise-groups-open-view-drawer',
      fetchGroupsList: 'enterprise-groups-fetch-groups-list'
    };
    var createDrawerSelectors = {
      addGroupBtn: '.masterstudy-account-enterprise-groups__add-group'
    };
    var importCsvModalSelectors = {
      importCsvBtn: '.masterstudy-account-enterprise-groups__import-csv'
    };
    var groupSelector = '.masterstudy-account-enterprise-groups__group';
    var visibleClass = 'masterstudy-account-enterprise-groups__group-header-floating-menu_visible';
    var hiddenClass = 'masterstudy-account-utility_hidden';
    var noOwnerMemberModifierClass = 'masterstudy-account-enterprise-groups__member-groups_no-owner-offset';
    var groupsContainer = $('.masterstudy-account-enterprise-groups__groups');
    var memberGroupsSection = $('.masterstudy-account-enterprise-groups__member-groups');
    var memberGroupsContainer = $('.masterstudy-account-enterprise-groups__member-groups-list');
    var groupsHeader = $('.masterstudy-account-enterprise-groups__header');
    var noRecordsEl = $('.masterstudy-account-enterprise-groups__no-records');
    var containerEl = $('.masterstudy-account-enterprise-groups');
    var groupsLoader = containerEl.find('.masterstudy-loader').first();
    var translations = stm_lms_groups['translations'];
    var groups = [];
    var memberGroups = [];
    var isLoading = false;
    var loadingRequests = 0;
    function startLoading() {
      loadingRequests += 1;
      isLoading = true;
      groupsLoader.css('display', 'flex');
    }
    function stopLoading() {
      loadingRequests = Math.max(loadingRequests - 1, 0);
      isLoading = loadingRequests > 0;
      groupsLoader.css('display', isLoading ? 'flex' : 'none');
    }
    function renderGroupsList(items, container) {
      var withActions = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;
      var groupTemplate = document.getElementById(withActions ? 'masterstudy-account-enterprise-groups__group' : 'masterstudy-account-enterprise-groups__member-group');
      var memberTemplate = document.getElementById('masterstudy-account-enterprise-groups__group-member');
      container.empty();
      items.forEach(function (group) {
        var clone = groupTemplate.content.cloneNode(true);
        var members = clone.querySelector("".concat(groupSelector, "-members"));
        var memberCounter = clone.querySelector("".concat(groupSelector, "-member-counter"));
        clone.querySelector("".concat(groupSelector, "-header-title")).textContent = group.title;
        clone.querySelector("".concat(groupSelector, "-stats-members")).textContent = "".concat(group.users.length, " ").concat(translations['members']);
        clone.querySelector("".concat(groupSelector, "-stats-courses")).textContent = "".concat(group.courses.length, " ").concat(translations['courses']);
        group.users.slice(0, 4).forEach(function (user) {
          var memberClone = memberTemplate.content.cloneNode(true);
          memberClone.querySelector("".concat(groupSelector, "-member img")).src = user.avatar;
          memberClone.querySelector("".concat(groupSelector, "-member-counter"));
          members.append(memberClone);
        });
        var floatingMenu = clone.querySelector("".concat(groupSelector, "-header-floating-menu"));
        if (withActions && floatingMenu) {
          var toggleMenu = function toggleMenu() {
            floatingMenu.classList.toggle('masterstudy-account-enterprise-groups__group-header-floating-menu_visible');
          };
          var _iterator = _createForOfIteratorHelper(floatingMenu.children),
            _step;
          try {
            for (_iterator.s(); !(_step = _iterator.n()).done;) {
              var item = _step.value;
              switch (item.getAttribute('data-action')) {
                case 'view':
                  item.addEventListener('click', function () {
                    dispatchGroupsCustomEvent(events.openViewDrawer, group);
                    toggleMenu();
                  });
                  break;
                case 'edit':
                  item.addEventListener('click', function () {
                    dispatchGroupsCustomEvent(events.openCreateDrawer, group);
                    toggleMenu();
                  });
                  break;
                case 'delete':
                  item.addEventListener('click', function () {
                    void deleteGroup(group);
                    toggleMenu();
                  });
                  break;
                default:
                  console.error('Unknown action item: ', item);
              }
            }
          } catch (err) {
            _iterator.e(err);
          } finally {
            _iterator.f();
          }
        }
        if (group.users.length > 4) {
          memberCounter.querySelector('span').textContent = "+".concat(group.users.length - 4);
          members.appendChild(memberCounter);
        } else {
          memberCounter.remove();
        }
        $(container).append(clone);
      });
    }
    function renderGroups() {
      renderGroupsList(groups, groupsContainer, true);
    }
    function renderMemberGroups() {
      if (!memberGroups.length) {
        memberGroupsContainer.empty();
        memberGroupsSection.toggleClass('masterstudy-account-utility_hidden', true);
        return;
      }
      renderGroupsList(memberGroups, memberGroupsContainer, false);
      memberGroupsSection.toggleClass('masterstudy-account-utility_hidden', false);
    }
    var renderNoGroups = function renderNoGroups(val) {
      if (val) {
        groupsContainer.empty();
        groupsHeader.toggleClass('masterstudy-account-utility_hidden', true);
        groupsContainer.toggleClass('masterstudy-account-utility_hidden', true);
        noRecordsEl.toggleClass('masterstudy-account-utility_hidden', false);
      } else {
        groupsHeader.toggleClass('masterstudy-account-utility_hidden', false);
        noRecordsEl.toggleClass('masterstudy-account-utility_hidden', true);
        groupsContainer.toggleClass('masterstudy-account-utility_hidden', false);
      }
    };
    function fetchGroups() {
      return _fetchGroups.apply(this, arguments);
    }
    function _fetchGroups() {
      _fetchGroups = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
        var url, res;
        return _regeneratorRuntime().wrap(function _callee$(_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              url = stm_lms_ajaxurl + '?action=stm_lms_get_enterprise_groups&nonce=' + stm_lms_nonces['stm_lms_get_enterprise_groups'];
              startLoading();
              renderNoGroups(false);
              _context.prev = 3;
              _context.next = 6;
              return fetch(url, {
                method: 'GET'
              });
            case 6:
              res = _context.sent;
              if (res.ok) {
                _context.next = 9;
                break;
              }
              return _context.abrupt("return");
            case 9:
              _context.next = 11;
              return res.json();
            case 11:
              groups = _context.sent;
              if (!groups.length) {
                renderNoGroups(true);
              } else {
                renderGroups();
              }
              _context.next = 18;
              break;
            case 15:
              _context.prev = 15;
              _context.t0 = _context["catch"](3);
              console.error(_context.t0);
            case 18:
              _context.prev = 18;
              stopLoading();
              return _context.finish(18);
            case 21:
            case "end":
              return _context.stop();
          }
        }, _callee, null, [[3, 15, 18, 21]]);
      }));
      return _fetchGroups.apply(this, arguments);
    }
    function fetchMemberGroups() {
      return _fetchMemberGroups.apply(this, arguments);
    }
    function _fetchMemberGroups() {
      _fetchMemberGroups = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee2() {
        var url, res;
        return _regeneratorRuntime().wrap(function _callee2$(_context2) {
          while (1) switch (_context2.prev = _context2.next) {
            case 0:
              url = stm_lms_ajaxurl + '?action=stm_lms_get_enterprise_member_groups&nonce=' + stm_lms_nonces['stm_lms_get_enterprise_member_groups'];
              startLoading();
              _context2.prev = 2;
              _context2.next = 5;
              return fetch(url, {
                method: 'GET'
              });
            case 5:
              res = _context2.sent;
              if (res.ok) {
                _context2.next = 8;
                break;
              }
              return _context2.abrupt("return");
            case 8:
              _context2.next = 10;
              return res.json();
            case 10:
              memberGroups = _context2.sent;
              renderMemberGroups();
              _context2.next = 17;
              break;
            case 14:
              _context2.prev = 14;
              _context2.t0 = _context2["catch"](2);
              console.error(_context2.t0);
            case 17:
              _context2.prev = 17;
              stopLoading();
              return _context2.finish(17);
            case 20:
            case "end":
              return _context2.stop();
          }
        }, _callee2, null, [[2, 14, 17, 20]]);
      }));
      return _fetchMemberGroups.apply(this, arguments);
    }
    function deleteGroup(_x) {
      return _deleteGroup.apply(this, arguments);
    }
    function _deleteGroup() {
      _deleteGroup = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee3(group) {
        var url, res;
        return _regeneratorRuntime().wrap(function _callee3$(_context3) {
          while (1) switch (_context3.prev = _context3.next) {
            case 0:
              if (!confirm("".concat(translations.delete_group_confirm, " ").concat(group.title, "?"))) {
                _context3.next = 21;
                break;
              }
              url = stm_lms_ajaxurl + '?action=stm_lms_delete_enterprise_group&nonce=' + stm_lms_nonces['stm_lms_delete_enterprise_group'] + '&group_id=' + group.group_id;
              startLoading();
              _context3.prev = 3;
              _context3.next = 6;
              return fetch(url, {
                method: 'GET'
              });
            case 6:
              res = _context3.sent;
              if (res.ok) {
                _context3.next = 9;
                break;
              }
              return _context3.abrupt("return");
            case 9:
              _context3.next = 11;
              return res.json();
            case 11:
              _context3.next = 13;
              return Promise.all([fetchGroups(), fetchMemberGroups()]);
            case 13:
              _context3.next = 18;
              break;
            case 15:
              _context3.prev = 15;
              _context3.t0 = _context3["catch"](3);
              console.error(_context3.t0);
            case 18:
              _context3.prev = 18;
              stopLoading();
              return _context3.finish(18);
            case 21:
            case "end":
              return _context3.stop();
          }
        }, _callee3, null, [[3, 15, 18, 21]]);
      }));
      return _deleteGroup.apply(this, arguments);
    }
    function init() {
      void Promise.all([fetchGroups(), fetchMemberGroups()]);
    }
    function dispatchGroupsCustomEvent(event) {
      var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      document.dispatchEvent(new CustomEvent(event, {
        detail: data
      }));
    }
    function bindGroupEvents(event, callback) {
      document.addEventListener(event, callback);
    }
    function bindContainerEvent(event, selector, callback) {
      $(containerEl).on(event, selector, callback);
    }
    init();
    bindContainerEvent('click', createDrawerSelectors.addGroupBtn, function () {
      dispatchGroupsCustomEvent(events.openCreateDrawer, {
        'group_id': '',
        'title': '',
        'emails': []
      });
    });
    bindGroupEvents(events.fetchGroupsList, function () {
      void Promise.all([fetchGroups(), fetchMemberGroups()]);
    });
    bindContainerEvent('click', importCsvModalSelectors.importCsvBtn, function () {
      dispatchGroupsCustomEvent(events.openImportCsvModal);
    });
    document.addEventListener('click', function (e) {
      if (isLoading) return;
      var actionBtn = e.target.closest("".concat(groupSelector, "-header-action"));
      if (!actionBtn) return;
      var groupEl = actionBtn.closest(groupSelector);
      var menu = groupEl === null || groupEl === void 0 ? void 0 : groupEl.querySelector("".concat(groupSelector, "-header-floating-menu"));
      if (!menu) return;
      document.querySelectorAll("".concat(groupSelector, "-header-floating-menu.").concat(visibleClass)).forEach(function (m) {
        return m !== menu && m.classList.remove(visibleClass);
      });
      menu.classList.toggle(visibleClass);
    });
    document.addEventListener('click', function (e) {
      if (isLoading) return;
      if (e.target.closest("".concat(groupSelector, "-header-floating-menu")) || e.target.closest("".concat(groupSelector, "-header-action"))) return;
      document.querySelectorAll("".concat(groupSelector, "-header-floating-menu.").concat(visibleClass)).forEach(function (m) {
        return m.classList.remove(visibleClass);
      });
    });
  });
})(jQuery);