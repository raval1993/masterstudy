"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, defineProperty = Object.defineProperty || function (obj, key, desc) { obj[key] = desc.value; }, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return defineProperty(generator, "_invoke", { value: makeInvokeMethod(innerFn, self, context) }), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; defineProperty(this, "_invoke", { value: function value(method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; } function maybeInvokeDelegate(delegate, context) { var methodName = context.method, method = delegate.iterator[methodName]; if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel; var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), defineProperty(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (val) { var object = Object(val), keys = []; for (var key in object) keys.push(key); return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter); }
function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
(function ($) {
  $(document).ready(function () {
    var events = {
      openViewDrawer: 'enterprise-groups-open-view-drawer',
      fetchGroupsList: 'enterprise-groups-fetch-groups-list'
    };
    var viewDrawerSelectors = {
      title: '.masterstudy-account-enterprise-groups__view-drawer-content__header-title',
      closeBtn: '.masterstudy-account-enterprise-groups__view-drawer-content__header-close',
      usersList: '.masterstudy-account-enterprise-groups__view-drawer-content__body',
      removeUserBtn: '.masterstudy-account-enterprise-groups__view-drawer-user-remove',
      setAsAdminBtn: '.masterstudy-account-enterprise-groups__view-drawer-user-set-admin',
      removeCourseBtn: '.masterstudy-account-enterprise-groups__view-drawer-course-remove',
      addCourseBtn: '.masterstudy-account-enterprise-groups__view-drawer-course-add',
      intersect: '.masterstudy-account-enterprise-groups__view-drawer-content-intersect'
    };
    var templatesSelectors = {
      course: '#masterstudy-account-enterprise-groups__view-drawer-course',
      user: '#masterstudy-account-enterprise-groups__view-drawer-user',
      noRecords: '#masterstudy-account-enterprise-groups__view-drawer-no-users'
    };
    var viewDrawerEl = $('.masterstudy-account-enterprise-groups__view-drawer');
    var userBaseSelector = '.masterstudy-account-enterprise-groups__view-drawer-user';
    var courseBaseSelector = '.masterstudy-account-enterprise-groups__view-drawer-course';
    var viewData = {
      'title': '',
      'data': null
    };
    var page = 1;
    var perPage = 10;
    var isAdminChanging = false;
    var translations = window.view_drawer.translations;
    var observer;
    var isUsersLoading = false;
    function toggleViewDrawer() {
      var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      viewDrawerEl.toggleClass('masterstudy-drawer-component_open', state);
    }
    function setIsLoading(state) {
      viewDrawerEl.find('.masterstudy-loader').css('display', state ? 'flex' : 'none');
      isUsersLoading = state;
    }
    function renderNoUsers(state) {
      if (state) {
        var noRecordsTemplate = document.querySelector(templatesSelectors.noRecords);
        $(viewDrawerSelectors.usersList).append(noRecordsTemplate.content.cloneNode(true));
      } else {
        $(viewDrawerSelectors.usersList).empty();
      }
    }
    function changeAdmin(_x) {
      return _changeAdmin.apply(this, arguments);
    }
    function _changeAdmin() {
      _changeAdmin = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee5(userId) {
        var url, res;
        return _regeneratorRuntime().wrap(function _callee5$(_context5) {
          while (1) switch (_context5.prev = _context5.next) {
            case 0:
              if (!isAdminChanging) {
                _context5.next = 2;
                break;
              }
              return _context5.abrupt("return");
            case 2:
              if (confirm(translations['admin_notice'])) {
                _context5.next = 4;
                break;
              }
              return _context5.abrupt("return");
            case 4:
              url = stm_lms_ajaxurl + '?action=stm_lms_change_ent_group_admin&user_id=' + userId + '&group_id=' + viewData.id + '&nonce=' + stm_lms_nonces['stm_lms_change_ent_group_admin'];
              isAdminChanging = true;
              _context5.prev = 6;
              _context5.next = 9;
              return fetch(url, {
                method: 'GET'
              });
            case 9:
              res = _context5.sent;
              if (res.ok) {
                _context5.next = 12;
                break;
              }
              return _context5.abrupt("return");
            case 12:
              toggleViewDrawer(false);
              dispatchGroupsCustomEvent(events.fetchGroupsList);
              _context5.next = 19;
              break;
            case 16:
              _context5.prev = 16;
              _context5.t0 = _context5["catch"](6);
              console.error(_context5.t0);
            case 19:
              _context5.prev = 19;
              isAdminChanging = false;
              return _context5.finish(19);
            case 22:
            case "end":
              return _context5.stop();
          }
        }, _callee5, null, [[6, 16, 19, 22]]);
      }));
      return _changeAdmin.apply(this, arguments);
    }
    function refetchUsers() {
      page = 1;
      perPage = 10;
      void loadGroupDetail();
    }
    function removeFromGroup(_x2) {
      return _removeFromGroup.apply(this, arguments);
    }
    function _removeFromGroup() {
      _removeFromGroup = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee6(userId) {
        var user, url, res;
        return _regeneratorRuntime().wrap(function _callee6$(_context6) {
          while (1) switch (_context6.prev = _context6.next) {
            case 0:
              if (confirm(translations['remove_notice'])) {
                _context6.next = 2;
                break;
              }
              return _context6.abrupt("return");
            case 2:
              user = viewData.data.data.find(function (user) {
                return String(user.user.id) === String(userId);
              }).user;
              url = stm_lms_ajaxurl + '?action=stm_lms_delete_user_from_group&user_id=' + user.id + '&user_email=' + user.email + '&group_id=' + viewData.id + '&nonce=' + stm_lms_nonces['stm_lms_delete_user_from_group'];
              _context6.prev = 4;
              _context6.next = 7;
              return fetch(url, {
                method: 'GET'
              });
            case 7:
              res = _context6.sent;
              if (res.ok) {
                _context6.next = 10;
                break;
              }
              return _context6.abrupt("return");
            case 10:
              _context6.next = 12;
              return res.json();
            case 12:
              refetchUsers();
              _context6.next = 18;
              break;
            case 15:
              _context6.prev = 15;
              _context6.t0 = _context6["catch"](4);
              console.error(_context6.t0);
            case 18:
            case "end":
              return _context6.stop();
          }
        }, _callee6, null, [[4, 15]]);
      }));
      return _removeFromGroup.apply(this, arguments);
    }
    function deleteUserCourse(_x3, _x4) {
      return _deleteUserCourse.apply(this, arguments);
    }
    function _deleteUserCourse() {
      _deleteUserCourse = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee7(userId, courseId) {
        var course, url, res;
        return _regeneratorRuntime().wrap(function _callee7$(_context7) {
          while (1) switch (_context7.prev = _context7.next) {
            case 0:
              course = viewData.data.data.find(function (user) {
                return String(user.user.id) === String(userId);
              }).courses.find(function (course) {
                return String(courseId) === String(course.course_id);
              });
              url = stm_lms_ajaxurl + '?action=stm_lms_delete_user_ent_courses&user_id=' + userId + '&group_id=' + course.group_id + '&course_id=' + course.course_id + '&nonce=' + stm_lms_nonces['stm_lms_delete_user_ent_courses'];
              _context7.prev = 2;
              _context7.next = 5;
              return fetch(url, {
                method: 'GET'
              });
            case 5:
              res = _context7.sent;
              if (res.ok) {
                _context7.next = 8;
                break;
              }
              return _context7.abrupt("return");
            case 8:
              _context7.next = 10;
              return res.json();
            case 10:
              course.added = false;
              renderUsers();
              _context7.next = 17;
              break;
            case 14:
              _context7.prev = 14;
              _context7.t0 = _context7["catch"](2);
              console.error(_context7.t0);
            case 17:
            case "end":
              return _context7.stop();
          }
        }, _callee7, null, [[2, 14]]);
      }));
      return _deleteUserCourse.apply(this, arguments);
    }
    function addUserCourse(_x5, _x6) {
      return _addUserCourse.apply(this, arguments);
    }
    function _addUserCourse() {
      _addUserCourse = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee8(userId, courseId) {
        var course, url, res;
        return _regeneratorRuntime().wrap(function _callee8$(_context8) {
          while (1) switch (_context8.prev = _context8.next) {
            case 0:
              course = viewData.data.data.find(function (user) {
                return String(user.user.id) === String(userId);
              }).courses.find(function (course) {
                return String(courseId) === String(course.course_id);
              });
              url = stm_lms_ajaxurl + '?action=stm_lms_add_user_ent_courses&user_id=' + userId + '&group_id=' + course.group_id + '&course_id=' + course.course_id + '&nonce=' + stm_lms_nonces['stm_lms_add_user_ent_courses'];
              _context8.prev = 2;
              _context8.next = 5;
              return fetch(url, {
                method: 'GET'
              });
            case 5:
              res = _context8.sent;
              if (res.ok) {
                _context8.next = 8;
                break;
              }
              return _context8.abrupt("return");
            case 8:
              _context8.next = 10;
              return res.json();
            case 10:
              course.added = true;
              renderUsers();
              _context8.next = 17;
              break;
            case 14:
              _context8.prev = 14;
              _context8.t0 = _context8["catch"](2);
              console.error(_context8.t0);
            case 17:
            case "end":
              return _context8.stop();
          }
        }, _callee8, null, [[2, 14]]);
      }));
      return _addUserCourse.apply(this, arguments);
    }
    function renderUsers() {
      var userTemplate = document.querySelector(templatesSelectors.user);
      var courseTemplate = document.querySelector(templatesSelectors.course);
      $(viewDrawerSelectors.usersList).empty();
      viewData.data.data.forEach(function (user) {
        var _userClone$querySelec;
        var userClone = userTemplate.content.cloneNode(true);
        userClone.querySelector(userBaseSelector).setAttribute('data-id', user.user.id);
        userClone.querySelector("".concat(userBaseSelector, "-info img")).src = user.user.avatar_url;
        userClone.querySelector("".concat(userBaseSelector, "-info img")).alt = user.user.login;
        userClone.querySelector("".concat(userBaseSelector, "-info span")).textContent = user.user.login;
        var coursesEl = user.courses.map(function (course) {
          var courseClone = courseTemplate.content.cloneNode(true);
          courseClone.querySelector(courseBaseSelector).setAttribute('data-id', course.course_id);
          courseClone.querySelector("".concat(courseBaseSelector, "-info img")).src = course.data.image;
          courseClone.querySelector("".concat(courseBaseSelector, "-info img")).alt = course.data.title;
          courseClone.querySelector("".concat(courseBaseSelector, "-info span")).textContent = course.data.title;
          if (course.added) {
            courseClone.querySelector("".concat(courseBaseSelector, "-add")).remove();
          } else {
            courseClone.querySelector("".concat(courseBaseSelector, "-remove-btn")).remove();
          }
          return courseClone;
        });
        (_userClone$querySelec = userClone.querySelector("".concat(userBaseSelector, "-courses"))).append.apply(_userClone$querySelec, _toConsumableArray(coursesEl));
        $(viewDrawerSelectors.usersList).append(userClone);
      });
    }
    function loadGroupDetail() {
      return _loadGroupDetail.apply(this, arguments);
    }
    function _loadGroupDetail() {
      _loadGroupDetail = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee9() {
        var url, _viewData$data, res;
        return _regeneratorRuntime().wrap(function _callee9$(_context9) {
          while (1) switch (_context9.prev = _context9.next) {
            case 0:
              if (!isUsersLoading) {
                _context9.next = 2;
                break;
              }
              return _context9.abrupt("return");
            case 2:
              url = stm_lms_ajaxurl + '?action=stm_lms_get_users_with_ent_courses&nonce=' + stm_lms_nonces['stm_lms_get_users_with_ent_courses'] + "&group_id=".concat(viewData.id, "&page=").concat(page, "&per_page=").concat(perPage);
              setIsLoading(true);
              if (page === 1) {
                renderNoUsers(false);
              }
              _context9.prev = 5;
              _context9.next = 8;
              return fetch(url, {
                method: 'GET'
              });
            case 8:
              res = _context9.sent;
              if (res.ok) {
                _context9.next = 11;
                break;
              }
              return _context9.abrupt("return");
            case 11:
              _context9.next = 13;
              return res.json();
            case 13:
              viewData.data = _context9.sent;
              if (!((_viewData$data = viewData.data) !== null && _viewData$data !== void 0 && (_viewData$data = _viewData$data.data) !== null && _viewData$data !== void 0 && _viewData$data.length) && page === 1) {
                renderNoUsers(true);
              } else {
                renderUsers();
              }
              _context9.next = 20;
              break;
            case 17:
              _context9.prev = 17;
              _context9.t0 = _context9["catch"](5);
              console.error(_context9.t0);
            case 20:
              _context9.prev = 20;
              setIsLoading(false);
              return _context9.finish(20);
            case 23:
            case "end":
              return _context9.stop();
          }
        }, _callee9, null, [[5, 17, 20, 23]]);
      }));
      return _loadGroupDetail.apply(this, arguments);
    }
    function bindGroupEvents(event, callback) {
      document.addEventListener(event, callback);
    }
    function bindContainerEvent(event, selector, callback) {
      $(viewDrawerEl).on(event, selector, callback);
    }
    function dispatchGroupsCustomEvent(event) {
      var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      document.dispatchEvent(new CustomEvent(event, {
        detail: data
      }));
    }
    bindGroupEvents(events.openViewDrawer, function (data) {
      toggleViewDrawer(true);
      var group = data.detail;
      viewData.title = group.title;
      viewData.id = group.group_id;
      viewData.data = null;
      $(viewDrawerSelectors.title).text(group.title);
      refetchUsers();
      if (!observer) {
        var _observer = new IntersectionObserver(function (entries, obs) {
          entries.forEach(function (entry) {
            if (!entry.isIntersecting || isUsersLoading) return;
            if (viewData.data.total > perPage * page) {
              page++;
              loadGroupDetail();
            }
          });
        }, {
          root: null,
          threshold: 0,
          rootMargin: "0px"
        });
        _observer.observe(viewDrawerEl.find(viewDrawerSelectors.intersect).get(0));
      }
    });
    bindContainerEvent('click', viewDrawerSelectors.closeBtn, function () {
      toggleViewDrawer(false);
    });
    bindContainerEvent('click', viewDrawerSelectors.removeUserBtn, /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
      var userId;
      return _regeneratorRuntime().wrap(function _callee$(_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            if (!$(this).hasClass('loading')) {
              _context.next = 2;
              break;
            }
            return _context.abrupt("return");
          case 2:
            userId = $(this).closest(userBaseSelector).data('id');
            $(this).addClass('loading');
            _context.prev = 4;
            _context.next = 7;
            return removeFromGroup(userId);
          case 7:
            _context.prev = 7;
            $(this).removeClass('loading');
            return _context.finish(7);
          case 10:
          case "end":
            return _context.stop();
        }
      }, _callee, this, [[4,, 7, 10]]);
    })));
    bindContainerEvent('click', viewDrawerSelectors.setAsAdminBtn, /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee2() {
      var userId;
      return _regeneratorRuntime().wrap(function _callee2$(_context2) {
        while (1) switch (_context2.prev = _context2.next) {
          case 0:
            userId = $(this).closest(userBaseSelector).data('id');
            $(this).addClass('loading');
            _context2.prev = 2;
            _context2.next = 5;
            return changeAdmin(userId);
          case 5:
            _context2.prev = 5;
            $(this).removeClass('loading');
            return _context2.finish(5);
          case 8:
          case "end":
            return _context2.stop();
        }
      }, _callee2, this, [[2,, 5, 8]]);
    })));
    bindContainerEvent('click', viewDrawerSelectors.removeCourseBtn, /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee3() {
      var courseId, userId;
      return _regeneratorRuntime().wrap(function _callee3$(_context3) {
        while (1) switch (_context3.prev = _context3.next) {
          case 0:
            if (!$(this).hasClass('loading')) {
              _context3.next = 2;
              break;
            }
            return _context3.abrupt("return");
          case 2:
            courseId = $(this).closest(courseBaseSelector).data('id');
            userId = $(this).closest(userBaseSelector).data('id');
            $(this).addClass('loading');
            _context3.prev = 5;
            _context3.next = 8;
            return deleteUserCourse(userId, courseId);
          case 8:
            _context3.prev = 8;
            $(this).removeClass('loading');
            return _context3.finish(8);
          case 11:
          case "end":
            return _context3.stop();
        }
      }, _callee3, this, [[5,, 8, 11]]);
    })));
    bindContainerEvent('click', viewDrawerSelectors.addCourseBtn, /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee4() {
      var courseId, userId;
      return _regeneratorRuntime().wrap(function _callee4$(_context4) {
        while (1) switch (_context4.prev = _context4.next) {
          case 0:
            if (!$(this).hasClass('loading')) {
              _context4.next = 2;
              break;
            }
            return _context4.abrupt("return");
          case 2:
            courseId = $(this).closest(courseBaseSelector).data('id');
            userId = $(this).closest(userBaseSelector).data('id');
            $(this).addClass('loading');
            _context4.prev = 5;
            _context4.next = 8;
            return addUserCourse(userId, courseId);
          case 8:
            _context4.prev = 8;
            $(this).removeClass('loading');
            return _context4.finish(8);
          case 11:
          case "end":
            return _context4.stop();
        }
      }, _callee4, this, [[5,, 8, 11]]);
    })));
  });
})(jQuery);