"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, defineProperty = Object.defineProperty || function (obj, key, desc) { obj[key] = desc.value; }, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return defineProperty(generator, "_invoke", { value: makeInvokeMethod(innerFn, self, context) }), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; defineProperty(this, "_invoke", { value: function value(method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; } function maybeInvokeDelegate(delegate, context) { var methodName = context.method, method = delegate.iterator[methodName]; if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel; var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), defineProperty(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (val) { var object = Object(val), keys = []; for (var key in object) keys.push(key); return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
(function ($) {
  $(document).ready(function () {
    var modals = $('.masterstudy-course-templates__modal');
    var mainContainer = $('.masterstudy-course-templates');
    setTimeout(function () {
      modals.removeAttr('style');
      mainContainer.removeAttr('style');
    }, 1000);
    var courseSelect = $('#masterstudy-course-select');
    if (courseSelect.length && typeof $.fn.select2 !== 'undefined') {
      courseSelect.select2({
        dropdownParent: mainContainer
      });
      courseSelect.on('select2:open', function () {
        setTimeout(function () {
          $('.select2-search__field').attr('placeholder', masterstudy_course_templates_data.find_course);
        }, 0);
      });
    }
    var currentPlace = '',
      itemToDelete = '',
      currentCategory = '',
      currentPost = '',
      currentCourse = '',
      postToDuplicate = '';

    // Listen for React CustomEvent to open the template modal
    window.addEventListener('masterstudy-open-course-templates', function (e) {
      var _e$detail;
      mainContainer.addClass('masterstudy-course-templates_open');
      $('body').addClass('masterstudy-course-templates_open');
      currentPlace = 'react_drawer';
      open_active_template(((_e$detail = e.detail) === null || _e$detail === void 0 ? void 0 : _e$detail.currentStyle) || 'default');
    });
    $(document).on('click', '#masterstudy-settings-course-change', function () {
      mainContainer.addClass('masterstudy-course-templates_open');
      $('body').addClass('masterstudy-course-templates_open');
      currentPlace = $(this).data('id');
      var currentStyle = $(this).data('current-style');
      if (currentStyle === '') {
        currentStyle = 'default';
      }
      open_active_template(currentStyle);
    });
    $(document).on('click', '.masterstudy-templates-choose-button', function () {
      mainContainer.addClass('masterstudy-course-templates_open');
      $('body').addClass('masterstudy-course-templates_open');
      currentPlace = $(this).data('id');
      currentCategory = $(this).data('term-id');
      var currentStyle = $(this).data('current-style');
      var myTemplate = masterstudy_course_templates_data.my_templates.find(function (tpl) {
        return tpl.name === currentStyle;
      });
      var nativeTemplate = masterstudy_course_templates_data.native_templates.find(function (tpl) {
        return tpl.name === currentStyle;
      });
      if (currentStyle === '' || !myTemplate && !nativeTemplate) {
        currentStyle = 'default';
      }
      open_active_template(currentStyle);
    });
    $(document).on('click', '.masterstudy-templates-reset-button', /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
      var currentPlace, currentCat, data, response, responseData, buttonTitle, buttonPopup, _buttonTitle, _buttonPopup;
      return _regeneratorRuntime().wrap(function _callee$(_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            currentPlace = $(this).data('id');
            if (!(currentPlace === 'edit_category')) {
              _context.next = 19;
              break;
            }
            currentCat = $(this).data('term-id');
            data = {
              course_style: 'none',
              term_id: parseInt(currentCat)
            };
            _context.prev = 4;
            _context.next = 7;
            return fetch("".concat(ms_lms_resturl, "/course-templates/assign-category-template"), {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': ms_lms_nonce
              },
              body: JSON.stringify(data)
            });
          case 7:
            response = _context.sent;
            _context.next = 10;
            return response.json();
          case 10:
            responseData = _context.sent;
            if (responseData) {
              $(this).prev().data('current-style', '');
              $(this).prev().find('.masterstudy-templates-choose-button__title').text(masterstudy_course_templates_data.none);
              $(this).prev().find('.masterstudy-hint__text').text(masterstudy_course_templates_data.none);
            } else {
              console.error('Error updating template:', responseData.errors);
            }
            _context.next = 17;
            break;
          case 14:
            _context.prev = 14;
            _context.t0 = _context["catch"](4);
            console.error('Error updating template:', _context.t0);
          case 17:
            _context.next = 20;
            break;
          case 19:
            if (currentPlace === 'new_category') {
              buttonTitle = $('[data-id="new_category"]').find('.masterstudy-templates-choose-button__title');
              buttonPopup = $('[data-id="new_category"]').find('.masterstudy-hint__text');
              $('[data-id="new_category"]').data('current-style', '');
              if (buttonTitle.length) {
                buttonTitle.text(masterstudy_course_templates_data.none);
                buttonPopup.text(masterstudy_course_templates_data.none);
              }
              $('#course_page_style').val('none');
            } else if (currentPlace === 'edit_category_inside') {
              _buttonTitle = $('[data-id="edit_category_inside"]').find('.masterstudy-templates-choose-button__title');
              _buttonPopup = $('[data-id="edit_category_inside"]').find('.masterstudy-hint__text');
              $('[data-id="edit_category_inside"]').data('current-style', '');
              if (_buttonTitle.length) {
                _buttonTitle.text(masterstudy_course_templates_data.none);
                _buttonPopup.text(masterstudy_course_templates_data.none);
              }
              $('#course_page_style').val('none');
            }
          case 20:
          case "end":
            return _context.stop();
        }
      }, _callee, this, [[4, 14]]);
    })));
    $(document).on('click', '.masterstudy-templates-page-button', function () {
      mainContainer.addClass('masterstudy-course-templates_open');
      $('body').addClass('masterstudy-course-templates_open');
      currentPlace = $(this).data('id');
      currentPost = $(this).data('post-id');
      currentCourse = $(this).data('current-course');
      var currentStyle = $(this).data('current-style');
      var activeInput = $("input[name=\"masterstudy_course_style\"][value=\"".concat(currentStyle, "\"]"));
      $('input[name="masterstudy_course_style"]').prop('checked', false);
      mainContainer.find('.masterstudy-course-templates__item-header').removeClass('masterstudy-course-templates__item-header_active');
      if (activeInput.length) {
        activeInput.prop('checked', true);
        activeInput.closest('.masterstudy-course-templates__item-wrapper').find('.masterstudy-course-templates__item-header').addClass('masterstudy-course-templates__item-header_active');
      } else {
        var firstInput = $('input[name="masterstudy_course_style"]').first();
        if (firstInput.length) {
          firstInput.prop('checked', true);
          var wrapper = firstInput.closest('.masterstudy-course-templates__item-wrapper');
          wrapper.find('.masterstudy-course-templates__item-header').addClass('masterstudy-course-templates__item-header_active');
        }
      }
      $('#my_templates.masterstudy-course-templates__list').addClass('masterstudy-course-templates__list_show');
      if (currentCourse) {
        courseSelect.val(currentCourse);
        courseSelect.trigger('change.select2');
      }
    });
    $(document).on('click', '.masterstudy-back-link', function (e) {
      e.preventDefault();
      mainContainer.removeClass('masterstudy-course-templates_open');
      $('body').removeClass('masterstudy-course-templates_open');
    });
    $(document).on('click', '.masterstudy-tabs__item', function () {
      var id = $(this).data('id');
      mainContainer.find('.masterstudy-course-templates__list').removeClass('masterstudy-course-templates__list_show');
      mainContainer.find('#' + id).addClass('masterstudy-course-templates__list_show');
      mainContainer.find('.masterstudy-tabs__item').removeClass('masterstudy-tabs__item_active');
      $(this).addClass('masterstudy-tabs__item_active');
    });
    $(document).on('click', 'input[name="masterstudy_course_style"]', function (e) {
      if ($(this).closest('.masterstudy-course-templates__item-wrapper').hasClass('masterstudy-course-templates__item-wrapper_disabled')) {
        e.preventDefault();
        e.stopImmediatePropagation();
        this.checked = false;
      }
    });
    $(document).on('click', '.masterstudy-course-templates__item-wrapper', function (e) {
      var isInsideLink = $(e.target).closest('a[data-id="masterstudy-template-preview"]').length > 0;
      var isLinkItself = $(e.target).is('a[data-id="masterstudy-template-preview"]');
      if (isInsideLink && !isLinkItself) {
        return;
      }
      if (isLinkItself) {
        return;
      }
      if ($(this).hasClass('masterstudy-course-templates__item-wrapper_library')) {
        return;
      }
      if ($(this).hasClass('masterstudy-course-templates__item-wrapper_disabled')) {
        return;
      }
      mainContainer.find('.masterstudy-course-templates__item-header').removeClass('masterstudy-course-templates__item-header_active');
      $(this).find('.masterstudy-course-templates__item-header').addClass('masterstudy-course-templates__item-header_active');
      $('input[name="masterstudy_course_style"]').prop('checked', false);
      $(this).find('input[name="masterstudy_course_style"]').prop('checked', true);
    });
    $(document).on('click', '.masterstudy-copy-template', function (e) {
      e.preventDefault();
      $('#masterstudy-course-templates-modal-create').addClass('masterstudy-course-templates__modal_open');
      postToDuplicate = $(this).data('id');
    });
    $(document).on('click', '.masterstudy-course-templates__item-copy', function (e) {
      e.preventDefault();
      $('#masterstudy-course-templates-modal-create').addClass('masterstudy-course-templates__modal_open');
      postToDuplicate = $(this).data('id');
    });
    $(document).on('click', '[data-id="masterstudy-modal-course-cancel"]', function (e) {
      e.preventDefault();
      var error = $(this).closest('.masterstudy-course-templates__modal').find('.masterstudy-course-templates__modal-error');
      $(this).closest('.masterstudy-course-templates__modal').removeClass('masterstudy-course-templates__modal_open');
      $('.masterstudy-course-templates__modal-input').val('');
      error.removeClass('masterstudy-course-templates__modal-error_show');
      itemToDelete = '';
      postToDuplicate = '';
    });
    $(document).on('click', '.masterstudy-course-templates__modal-close', function (e) {
      e.preventDefault();
      $(this).closest('.masterstudy-course-templates__modal').removeClass('masterstudy-course-templates__modal_open');
      $('.masterstudy-course-templates__modal-input').val('');
    });
    $(document).on('click', '.masterstudy-course-templates__item-title-edit, .masterstudy-course-templates__item-title-text', function (e) {
      e.stopPropagation();
      var list = $(this).closest('.masterstudy-course-templates__list');
      if (list.attr('id') !== 'my_templates') return;
      var parent = $(this).closest('.masterstudy-course-templates__item-title');
      var activeTitle = $('.masterstudy-course-templates__item-title_active');
      if (activeTitle.length && activeTitle.is(parent)) {
        saveTemplateTitle(activeTitle);
        activeTitle.removeClass('masterstudy-course-templates__item-title_active');
        activeTitle.find('.masterstudy-course-templates__item-title-text').attr('contenteditable', 'false');
        return;
      }
      if (activeTitle.length && !activeTitle.is(parent)) {
        saveTemplateTitle(activeTitle);
        activeTitle.removeClass('masterstudy-course-templates__item-title_active');
        activeTitle.find('.masterstudy-course-templates__item-title-text').attr('contenteditable', 'false');
      }
      if (!parent.hasClass('masterstudy-course-templates__item-title_active')) {
        parent.addClass('masterstudy-course-templates__item-title_active');
        parent.find('.masterstudy-course-templates__item-title-text').attr('contenteditable', 'true').focus();
      }
    });
    $(document).on('click', function (e) {
      var activeTitle = $('.masterstudy-course-templates__item-title_active');
      if (!activeTitle.length) return;
      if ($(e.target).closest('.masterstudy-course-templates__item-title').is(activeTitle)) return;
      saveTemplateTitle(activeTitle);
      activeTitle.removeClass('masterstudy-course-templates__item-title_active');
    });
    function saveTemplateTitle(titleBlock) {
      var item = titleBlock.closest('.masterstudy-course-templates__item');
      var newTitle = titleBlock.find('.masterstudy-course-templates__item-title-text').text().trim();
      var postId = item.attr('id');
      if (!newTitle) return;
      titleBlock.addClass('masterstudy-button_loading');
      fetch("".concat(ms_lms_resturl, "/course-templates/modify-template"), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': ms_lms_nonce
        },
        body: JSON.stringify({
          title: newTitle,
          post_id: postId
        })
      }).then(function (res) {
        return res.json();
      }).then(function (responseData) {
        if (responseData.status !== 'success') {
          console.error('Error updating template title:', responseData.errors);
        }
      })["catch"](function (err) {
        console.error('Error updating template title:', err);
      })["finally"](function () {
        titleBlock.removeClass('masterstudy-button_loading');
      });
    }
    $(document).on('input', '.masterstudy-course-templates__modal-input', function () {
      var error = $(this).closest('.masterstudy-course-templates__modal').find('.masterstudy-course-templates__modal-error');
      error.removeClass('masterstudy-course-templates__modal-error_show');
    });
    $(document).on('click', '.masterstudy-course-templates__add', function () {
      $('#masterstudy-course-templates-modal-new').addClass('masterstudy-course-templates__modal_open');
    });
    $(document).on('click', '[data-id="masterstudy-modal-course-template-new"]', /*#__PURE__*/function () {
      var _ref2 = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee2(e) {
        var input, error, _this, data, response, responseData;
        return _regeneratorRuntime().wrap(function _callee2$(_context2) {
          while (1) switch (_context2.prev = _context2.next) {
            case 0:
              e.preventDefault();
              input = $(this).closest('.masterstudy-course-templates__modal').find('.masterstudy-course-templates__modal-input');
              error = $(this).closest('.masterstudy-course-templates__modal').find('.masterstudy-course-templates__modal-error');
              if (!(input.val() === '')) {
                _context2.next = 8;
                break;
              }
              error.addClass('masterstudy-course-templates__modal-error_show');
              return _context2.abrupt("return");
            case 8:
              error.removeClass('masterstudy-course-templates__modal-error_show');
            case 9:
              _this = $(this);
              data = {
                title: input.val()
              };
              _context2.prev = 11;
              // Start the loading animation
              _this.addClass('masterstudy-button_loading');

              // Make the API call
              _context2.next = 15;
              return fetch("".concat(ms_lms_resturl, "/course-templates/create-template"), {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-WP-Nonce': ms_lms_nonce
                },
                body: JSON.stringify(data)
              });
            case 15:
              response = _context2.sent;
              _context2.next = 18;
              return response.json();
            case 18:
              responseData = _context2.sent;
              // Check if the response was successful
              if (responseData.status === 'success') {
                // Handle success
                add_new_template(responseData.template);
                _this.closest('.masterstudy-course-templates__modal').removeClass('masterstudy-course-templates__modal_open');
                input.val('');
              } else {
                // Handle failure
                console.error('Error creating template:', responseData.errors);
              }
              _context2.next = 25;
              break;
            case 22:
              _context2.prev = 22;
              _context2.t0 = _context2["catch"](11);
              // Handle any errors that occurred during the fetch
              console.error('Error creating template:', _context2.t0);
            case 25:
              _context2.prev = 25;
              // Remove the loading animation, whether success or error
              _this.removeClass('masterstudy-button_loading');
              return _context2.finish(25);
            case 28:
            case "end":
              return _context2.stop();
          }
        }, _callee2, this, [[11, 22, 25, 28]]);
      }));
      return function (_x) {
        return _ref2.apply(this, arguments);
      };
    }());
    $(document).on('click', '[data-id="masterstudy-modal-course-template-copy"]', /*#__PURE__*/function () {
      var _ref3 = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee3(e) {
        var input, error, _this, data, response, responseData;
        return _regeneratorRuntime().wrap(function _callee3$(_context3) {
          while (1) switch (_context3.prev = _context3.next) {
            case 0:
              e.preventDefault();
              input = $(this).closest('.masterstudy-course-templates__modal').find('.masterstudy-course-templates__modal-input');
              error = $(this).closest('.masterstudy-course-templates__modal').find('.masterstudy-course-templates__modal-error');
              if (input.val() === '') {
                error.addClass('masterstudy-course-templates__modal-error_show');
              } else {
                error.removeClass('masterstudy-course-templates__modal-error_show');
              }
              if (postToDuplicate) {
                _context3.next = 6;
                break;
              }
              return _context3.abrupt("return");
            case 6:
              _this = $(this);
              data = {
                title: input.val(),
                duplicate_id: parseInt(postToDuplicate)
              };
              _this.addClass('masterstudy-button_loading');
              _context3.prev = 9;
              _context3.next = 12;
              return fetch("".concat(ms_lms_resturl, "/course-templates/duplicate-template"), {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-WP-Nonce': ms_lms_nonce
                },
                body: JSON.stringify(data)
              });
            case 12:
              response = _context3.sent;
              _context3.next = 15;
              return response.json();
            case 15:
              responseData = _context3.sent;
              if (responseData.status === 'success') {
                _this.removeClass('masterstudy-button_loading');
                add_new_template(responseData.template);
                _this.closest('.masterstudy-course-templates__modal').removeClass('masterstudy-course-templates__modal_open');
                input.val('');
                postToDuplicate = '';
                mainContainer.find('.masterstudy-tabs__item').removeClass('masterstudy-tabs__item_active');
                mainContainer.find('.masterstudy-tabs__item[data-id="my_templates"]').addClass('masterstudy-tabs__item_active');
                $('.masterstudy-course-templates__list').removeClass('masterstudy-course-templates__list_show');
                $('#my_templates.masterstudy-course-templates__list').addClass('masterstudy-course-templates__list_show');
              } else {
                console.error('Error updating template:', responseData.errors);
              }
              _context3.next = 22;
              break;
            case 19:
              _context3.prev = 19;
              _context3.t0 = _context3["catch"](9);
              console.error('Error updating template:', _context3.t0);
            case 22:
              _context3.prev = 22;
              _this.removeClass('masterstudy-button_loading');
              return _context3.finish(22);
            case 25:
            case "end":
              return _context3.stop();
          }
        }, _callee3, this, [[9, 19, 22, 25]]);
      }));
      return function (_x2) {
        return _ref3.apply(this, arguments);
      };
    }());
    $(document).on('click', '.masterstudy-course-templates__item-delete', function () {
      $('#masterstudy-course-templates-modal-delete').addClass('masterstudy-course-templates__modal_open');
      itemToDelete = $(this).data('id');
    });
    $(document).on('click', '[data-id="masterstudy-modal-course-template-delete"]', /*#__PURE__*/function () {
      var _ref4 = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee4(e) {
        var _this, data, response, responseData, tab, currentCount;
        return _regeneratorRuntime().wrap(function _callee4$(_context4) {
          while (1) switch (_context4.prev = _context4.next) {
            case 0:
              e.preventDefault();
              if (itemToDelete) {
                _context4.next = 3;
                break;
              }
              return _context4.abrupt("return");
            case 3:
              _this = $(this);
              data = {
                post_id: parseInt(itemToDelete)
              };
              _this.addClass('masterstudy-button_loading');
              _context4.prev = 6;
              _context4.next = 9;
              return fetch("".concat(ms_lms_resturl, "/course-templates/delete-template/").concat(data.post_id), {
                method: 'DELETE',
                headers: {
                  'Content-Type': 'application/json',
                  'X-WP-Nonce': ms_lms_nonce
                }
              });
            case 9:
              response = _context4.sent;
              _context4.next = 12;
              return response.json();
            case 12:
              responseData = _context4.sent;
              if (responseData.status === 'success') {
                _this.removeClass('masterstudy-button_loading');
                $("#my_templates #".concat(itemToDelete)).remove();
                tab = $('.masterstudy-tabs__item[data-id="my_templates"]').find('.masterstudy-tabs__item-hint');
                currentCount = parseInt(tab.text(), 10) || 0;
                tab.text(currentCount - 1);
                _this.closest('.masterstudy-course-templates__modal').removeClass('masterstudy-course-templates__modal_open');
                itemToDelete = '';
              } else {
                console.error('Error updating template:', responseData.errors);
              }
              _context4.next = 19;
              break;
            case 16:
              _context4.prev = 16;
              _context4.t0 = _context4["catch"](6);
              console.error('Error updating template:', _context4.t0);
            case 19:
              _context4.prev = 19;
              _this.removeClass('masterstudy-button_loading');
              return _context4.finish(19);
            case 22:
            case "end":
              return _context4.stop();
          }
        }, _callee4, this, [[6, 16, 19, 22]]);
      }));
      return function (_x3) {
        return _ref4.apply(this, arguments);
      };
    }());
    $(document).on('click', '[data-id="masterstudy-save-course-template"]', /*#__PURE__*/function () {
      var _ref5 = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee5(e) {
        var selectedValue, _this, data, response, responseData, input, _selectedValue, selectedTitle, _this2, _data, _response, _responseData, buttonTitle, buttonPopup, _input, _selectedValue2, _selectedTitle, _buttonTitle2, _buttonPopup2, _input2, _selectedValue3, _selectedTitle2, _buttonTitle3, _buttonPopup3, _input3, _selectedValue4, _selectedTitle3, _input4, _selectedValue5, _selectedTitle4, _this3, _data2, _response2, _responseData2, button;
        return _regeneratorRuntime().wrap(function _callee5$(_context5) {
          while (1) switch (_context5.prev = _context5.next) {
            case 0:
              e.preventDefault();
              if (!(currentPlace === 'edit_settings')) {
                _context5.next = 26;
                break;
              }
              selectedValue = $('input[name="masterstudy_course_style"]:checked').val();
              _this = $(this);
              if (selectedValue) {
                _context5.next = 6;
                break;
              }
              return _context5.abrupt("return");
            case 6:
              data = {
                course_style: selectedValue
              };
              _this.addClass('masterstudy-button_loading');
              _context5.prev = 8;
              _context5.next = 11;
              return fetch("".concat(ms_lms_resturl, "/course-templates/update-template"), {
                method: 'PUT',
                headers: {
                  'Content-Type': 'application/json',
                  'X-WP-Nonce': ms_lms_nonce
                },
                body: JSON.stringify(data)
              });
            case 11:
              response = _context5.sent;
              _context5.next = 14;
              return response.json();
            case 14:
              responseData = _context5.sent;
              if (responseData.status === 'success') {
                window.dispatchEvent(new CustomEvent('masterstudy-course-template-changed', {
                  detail: selectedValue
                }));
                queueMicrotask(function () {
                  change_template_settings(selectedValue);
                });
                mainContainer.removeClass('masterstudy-course-templates_open');
                $('body').removeClass('masterstudy-course-templates_open');
              } else {
                console.error('Error updating template:', responseData.errors);
              }
              _context5.next = 21;
              break;
            case 18:
              _context5.prev = 18;
              _context5.t0 = _context5["catch"](8);
              console.error('Error updating template:', _context5.t0);
            case 21:
              _context5.prev = 21;
              _this.removeClass('masterstudy-button_loading');
              return _context5.finish(21);
            case 24:
              _context5.next = 113;
              break;
            case 26:
              if (!(currentPlace === 'edit_category')) {
                _context5.next = 53;
                break;
              }
              input = $('input[name="masterstudy_course_style"]:checked');
              _selectedValue = input.val();
              selectedTitle = input.closest('.masterstudy-course-templates__item-bottom').find('.masterstudy-course-templates__item-title-text').text();
              _this2 = $(this);
              if (_selectedValue) {
                _context5.next = 33;
                break;
              }
              return _context5.abrupt("return");
            case 33:
              _data = {
                course_style: _selectedValue,
                term_id: parseInt(currentCategory)
              };
              _this2.addClass('masterstudy-button_loading');
              _context5.prev = 35;
              _context5.next = 38;
              return fetch("".concat(ms_lms_resturl, "/course-templates/assign-category-template"), {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-WP-Nonce': ms_lms_nonce
                },
                body: JSON.stringify(_data)
              });
            case 38:
              _response = _context5.sent;
              _context5.next = 41;
              return _response.json();
            case 41:
              _responseData = _context5.sent;
              if (_responseData) {
                buttonTitle = $("[data-term-id=\"".concat(currentCategory, "\"]")).find('.masterstudy-templates-choose-button__title');
                buttonPopup = $("[data-term-id=\"".concat(currentCategory, "\"]")).find('.masterstudy-hint__text');
                $("[data-term-id=\"".concat(currentCategory, "\"]")).data('current-style', _selectedValue);
                if (buttonTitle.length) {
                  buttonTitle.text(selectedTitle);
                  buttonPopup.text(selectedTitle);
                }
                _this2.removeClass('masterstudy-button_loading');
                mainContainer.removeClass('masterstudy-course-templates_open');
                $('body').removeClass('masterstudy-course-templates_open');
              } else {
                console.error('Error updating template:', _responseData.errors);
              }
              _context5.next = 48;
              break;
            case 45:
              _context5.prev = 45;
              _context5.t1 = _context5["catch"](35);
              console.error('Error updating template:', _context5.t1);
            case 48:
              _context5.prev = 48;
              _this2.removeClass('masterstudy-button_loading');
              return _context5.finish(48);
            case 51:
              _context5.next = 113;
              break;
            case 53:
              if (!(currentPlace === 'new_category')) {
                _context5.next = 66;
                break;
              }
              _input = $('input[name="masterstudy_course_style"]:checked');
              _selectedValue2 = _input.val();
              _selectedTitle = _input.closest('.masterstudy-course-templates__item-bottom').find('.masterstudy-course-templates__item-title-text').text();
              _buttonTitle2 = $('[data-id="new_category"]').find('.masterstudy-templates-choose-button__title');
              _buttonPopup2 = $('[data-id="new_category"]').find('.masterstudy-hint__text');
              $('[data-id="new_category"]').data('current-style', _selectedValue2);
              if (_buttonTitle2.length) {
                _buttonTitle2.text(_selectedTitle);
                _buttonPopup2.text(_selectedTitle);
              }
              $('#course_page_style').val(_selectedValue2);
              mainContainer.removeClass('masterstudy-course-templates_open');
              $('body').removeClass('masterstudy-course-templates_open');
              _context5.next = 113;
              break;
            case 66:
              if (!(currentPlace === 'edit_category_inside')) {
                _context5.next = 79;
                break;
              }
              _input2 = $('input[name="masterstudy_course_style"]:checked');
              _selectedValue3 = _input2.val();
              _selectedTitle2 = _input2.closest('.masterstudy-course-templates__item-bottom').find('.masterstudy-course-templates__item-title-text').text();
              _buttonTitle3 = $('[data-id="edit_category_inside"]').find('.masterstudy-templates-choose-button__title');
              _buttonPopup3 = $('[data-id="edit_category_inside"]').find('.masterstudy-hint__text');
              $('[data-id="edit_category_inside"]').data('current-style', _selectedValue3);
              if (_buttonTitle3.length) {
                _buttonTitle3.text(_selectedTitle2);
                _buttonPopup3.text(_selectedTitle2);
              }
              $('#course_page_style').val(_selectedValue3);
              mainContainer.removeClass('masterstudy-course-templates_open');
              $('body').removeClass('masterstudy-course-templates_open');
              _context5.next = 113;
              break;
            case 79:
              if (!(currentPlace === 'react_drawer')) {
                _context5.next = 88;
                break;
              }
              _input3 = $('input[name="masterstudy_course_style"]:checked');
              _selectedValue4 = _input3.val();
              _selectedTitle3 = _input3.closest('.masterstudy-course-templates__item-bottom').find('.masterstudy-course-templates__item-title-text').text();
              window.dispatchEvent(new CustomEvent('masterstudy-course-template-selected', {
                detail: {
                  style: _selectedValue4,
                  title: _selectedTitle3.trim()
                }
              }));
              mainContainer.removeClass('masterstudy-course-templates_open');
              $('body').removeClass('masterstudy-course-templates_open');
              _context5.next = 113;
              break;
            case 88:
              if (!(currentPlace === 'edit_page')) {
                _context5.next = 113;
                break;
              }
              _input4 = $('input[name="masterstudy_course_style"]:checked');
              _selectedValue5 = _input4.val();
              _selectedTitle4 = _input4.closest('.masterstudy-course-templates__item-bottom').find('.masterstudy-course-templates__item-title-text').text();
              _this3 = $(this);
              if (_selectedValue5) {
                _context5.next = 95;
                break;
              }
              return _context5.abrupt("return");
            case 95:
              _data2 = {
                course_style: _selectedValue5,
                course_id: parseInt(courseSelect.val()),
                post_id: parseInt(currentPost)
              };
              _this3.addClass('masterstudy-button_loading');
              _context5.prev = 97;
              _context5.next = 100;
              return fetch("".concat(ms_lms_resturl, "/course-templates/page-to-course-template"), {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-WP-Nonce': ms_lms_nonce
                },
                body: JSON.stringify(_data2)
              });
            case 100:
              _response2 = _context5.sent;
              _context5.next = 103;
              return _response2.json();
            case 103:
              _responseData2 = _context5.sent;
              if (_responseData2.status === 'success') {
                button = $("[data-post-id=\"".concat(currentPost, "\"]"));
                if (button.length) {
                  button.find('.masterstudy-templates-page-button__template').text(_selectedTitle4);
                  button.data('current-style', _selectedValue5);
                  button.data('current-course', courseSelect.val());
                  if (_responseData2.course) {
                    button.find('.masterstudy-templates-page-button__course').text(_responseData2.course);
                  }
                }
                _this3.removeClass('masterstudy-button_loading');
                mainContainer.removeClass('masterstudy-course-templates_open');
                $('body').removeClass('masterstudy-course-templates_open');
              } else {
                console.error('Error updating template:', _responseData2.errors);
              }
              _context5.next = 110;
              break;
            case 107:
              _context5.prev = 107;
              _context5.t2 = _context5["catch"](97);
              console.error('Error updating template:', _context5.t2);
            case 110:
              _context5.prev = 110;
              _this3.removeClass('masterstudy-button_loading');
              return _context5.finish(110);
            case 113:
            case "end":
              return _context5.stop();
          }
        }, _callee5, this, [[8, 18, 21, 24], [35, 45, 48, 51], [97, 107, 110, 113]]);
      }));
      return function (_x4) {
        return _ref5.apply(this, arguments);
      };
    }());
    function open_active_template(currentStyle) {
      var activeInput = $("input[name=\"masterstudy_course_style\"][value=\"".concat(currentStyle, "\"]"));
      var activePanel = activeInput.closest('.masterstudy-course-templates__list');
      var activeTabId = activePanel.attr('id');
      $('input[name="masterstudy_course_style"]').prop('checked', false);
      $('.masterstudy-course-templates__list').removeClass('masterstudy-course-templates__list_show');
      mainContainer.find('.masterstudy-course-templates__item-header').removeClass('masterstudy-course-templates__item-header_active');
      mainContainer.find('.masterstudy-tabs__item').removeClass('masterstudy-tabs__item_active');
      activeInput.prop('checked', true);
      activeInput.closest('.masterstudy-course-templates__item-wrapper').find('.masterstudy-course-templates__item-header').addClass('masterstudy-course-templates__item-header_active');
      activePanel.addClass('masterstudy-course-templates__list_show');
      mainContainer.find(".masterstudy-tabs__item[data-id=\"".concat(activeTabId, "\"]")).addClass('masterstudy-tabs__item_active');
    }
    function add_new_template(template) {
      var newItem = "\n\t\t\t\t<div id=\"".concat(template.id, "\" class=\"masterstudy-course-templates__item\">\n\t\t\t\t\t<div class=\"masterstudy-course-templates__item-wrapper\">\n\t\t\t\t\t\t<div class=\"masterstudy-course-templates__item-header\">\n\t\t\t\t\t\t\t<div class=\"masterstudy-course-templates__item-bottom\">\n\t\t\t\t\t\t\t\t<input\n\t\t\t\t\t\t\t\t\ttype=\"radio\"\n\t\t\t\t\t\t\t\t\tname=\"masterstudy_course_style\"\n\t\t\t\t\t\t\t\t\tvalue=\"").concat(template.name, "\"\n\t\t\t\t\t\t\t\t\tclass=\"masterstudy-course-templates__item-input\"\n\t\t\t\t\t\t\t\t>\n\t\t\t\t\t\t\t\t<div class=\"masterstudy-course-templates__item-title\">\n\t\t\t\t\t\t\t\t\t<div contenteditable=\"true\" class=\"masterstudy-course-templates__item-title-text\">\n\t\t\t\t\t\t\t\t\t\t").concat(template.title, "\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t<span class=\"masterstudy-course-templates__item-title-edit\"></span>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t<div class=\"masterstudy-course-templates__item-hint\">\n\t\t\t\t\t\t\t\t<a data-id=\"masterstudy-elementor-edit\" href=\"").concat(masterstudy_course_templates_data.edit_url).concat(template.id, "&action=elementor\" class=\"masterstudy-course-templates__item-elementor\" target=\"_blank\">\n\t\t\t\t\t\t\t\t\t").concat(masterstudy_course_templates_data.edit_text, "\n\t\t\t\t\t\t\t\t</a>\n\t\t\t\t\t\t\t\t<a data-id=\"masterstudy-template-preview\" href=\"").concat(masterstudy_course_templates_data.my_preview_url, "?course_style=").concat(template.name, "\" class=\"masterstudy-course-templates__item-preview\" target=\"_blank\">\n\t\t\t\t\t\t\t\t\t").concat(masterstudy_course_templates_data.preview, "\n\t\t\t\t\t\t\t\t</a>\n\t\t\t\t\t\t\t\t<span data-id=\"").concat(template.id, "\" class=\"masterstudy-course-templates__item-copy\"></span>\n\t\t\t\t\t\t\t\t<span data-id=\"").concat(template.id, "\" class=\"masterstudy-course-templates__item-delete\"></span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t");
      $('#my_templates').append(newItem);
      var tab = $('.masterstudy-tabs__item[data-id="my_templates"]').find('.masterstudy-tabs__item-hint');
      var currentCount = parseInt(tab.text(), 10) || 0;
      tab.text(currentCount + 1);
    }
    function change_template_settings(template_name) {
      if (!template_name) return;
      var item = $(".masterstudy-course-templates__item-input[value=\"".concat(template_name, "\"]")).closest('.masterstudy-course-templates__item');
      if (!item.length) return;
      var id = item.attr('id');
      var template_title = item.find('.masterstudy-course-templates__item-title-text').text().trim();
      var name = template_name;
      var isElementor = item.find('[data-id="masterstudy-elementor-edit"]').length > 0;
      var img = $('.masterstudy-course-templates-settings__image');
      if (img.length) {
        var nativeTemplate = masterstudy_course_templates_data.native_templates.find(function (tpl) {
          return tpl.name === name;
        });
        var imgSrc = nativeTemplate ? masterstudy_course_templates_data.img_url + name + '.png' : masterstudy_course_templates_data.img_url + 'empty-layout.png';
        img.attr('src', imgSrc);
      }
      var title = $('.masterstudy-course-templates-settings__title');
      if (title.length) {
        title.html(template_title);
      }
      var preview = $('.masterstudy-course-templates-settings__link');
      if (preview.length) {
        preview.attr('href', masterstudy_course_templates_data.preview_url + name);
        preview.html(masterstudy_course_templates_data.preview);
        isElementor ? preview.hide() : preview.show();
      }
      var edit = $('#masterstudy-settings-course-edit');
      if (edit.length) {
        if (isElementor) {
          edit.attr('href', masterstudy_course_templates_data.edit_url + id + '&action=elementor');
          edit.html(masterstudy_course_templates_data.edit);
          edit.show();
        } else {
          edit.hide();
        }
      }
      var change = $('#masterstudy-settings-course-change');
      if (change.length) {
        change.data('current-style', name);
      }
      var input = $('input[name="course_style"]');
      if (input.length) {
        input.val(name);
      }
    }
  });
})(jQuery);