"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, defineProperty = Object.defineProperty || function (obj, key, desc) { obj[key] = desc.value; }, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return defineProperty(generator, "_invoke", { value: makeInvokeMethod(innerFn, self, context) }), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; defineProperty(this, "_invoke", { value: function value(method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; } function maybeInvokeDelegate(delegate, context) { var methodName = context.method, method = delegate.iterator[methodName]; if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel; var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), defineProperty(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (val) { var object = Object(val), keys = []; for (var key in object) keys.push(key); return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
(function ($) {
  var lesson = pdf_lesson;
  var pdfFile = lesson.pdf_file.url;
  var pdfFileName = lesson.pdf_file.label;
  var pdfReadAll = lesson.pdf_read_all;
  var translations = pdf_lesson.translations;
  var MAX_ZOOM_SCALE = 5;
  var MIN_ZOOM_SCALE = 0.5;
  var MOBILE_SCREEN_SIZE = 476;
  var MOBILE_WIDTH = window.innerWidth <= MOBILE_SCREEN_SIZE;
  var IS_MOBILE = isMobileDevice() || MOBILE_WIDTH;
  var CLIENT_WIDTH_OFFSET_CHECK = IS_MOBILE ? 0 : 120;
  var CLIENT_WIDTH_OFFSET_SET = IS_MOBILE ? 40 : 20;
  var LOCAL_STORAGE_PAGE_KEY = 'pdf_lesson_page';
  function isMobileDevice() {
    return /Mobi|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
  }
  var pdfDoc;
  var pageNum = 1;
  var scale = MOBILE_WIDTH ? 0.5 : 1;
  var isDragging = false;
  var isExpanded = false;
  var dragStart = {
    x: 0,
    y: 0
  };
  var canvasPosition = {
    x: 0,
    y: 0
  };
  var iframe;
  var initialViewport;
  var isTooltipVisible = false;
  var canvasRef = document.querySelector('.masterstudy-pdf-container__pdf-view');
  var canvasContainerRef = document.querySelector('.masterstudy-pdf-container__canvas');
  var contentWrapper = document.querySelector('.masterstudy-course-player-content__wrapper');
  var totalPagesRef = document.querySelector('.masterstudy-toolbar__total_pages');
  var pagesInputRef = document.querySelector('#toolbar__pages-input');
  var zoomValueRef = $('.masterstudy-toolbar__zoom-value');
  var zoomInBtnRef = document.querySelector('.masterstudy-toolbar__zoom-in-btn');
  var zoomOutBtnRef = document.querySelector('.masterstudy-toolbar__zoom-out-btn');
  var expandBtnRef = $('.masterstudy-toolbar__expand-btn');
  var toolbarTooltipRef = $('.masterstudy-toolbar__menu-tooltip');
  var toolbarContainer = $('.masterstudy-pdf-container__toolbar');
  var bookmarksList = $('.masterstudy-bookmarks__list');
  var submitButton = $('[data-id="masterstudy-course-player-lesson-submit"]');
  var submitHint = $(submitButton).parent().find('.masterstudy-hint');
  var backBtn = $('.masterstudy-pdf-container__back-btn:visible');
  var nextBtn = $('.masterstudy-pdf-container__next-btn:visible');
  var textLayerDiv = document.querySelector('.textLayer');
  var linkService;
  $(toolbarTooltipRef).appendTo('.masterstudy-course-player-content');
  if (pdfReadAll) {
    submitButton.attr('disabled', 1);
    submitButton.addClass('masterstudy-button_disabled');
  }
  if (isMobileDevice()) {
    $('.masterstudy-pdf-container').css('--masterstudy-mobile-toolbar-offset', '130px');
  }
  if (isMobileDevice()) {
    expandBtnRef.css('display', 'none');
  }
  var ctx = canvasRef.getContext('2d');
  var observe = new ResizeObserver(function () {
    var clientWidth = contentWrapper.clientWidth - CLIENT_WIDTH_OFFSET_CHECK;
    if (canvasRef.offsetWidth < clientWidth) {
      canvasContainerRef.style.width = "".concat(canvasRef.offsetWidth, "px");
    } else if (canvasContainerRef.offsetWidth >= clientWidth) {
      canvasContainerRef.style.width = "".concat(clientWidth - CLIENT_WIDTH_OFFSET_SET, "px");
    } else if (canvasContainerRef.offsetWidth < canvasRef.width) {
      canvasContainerRef.style.width = "".concat(Math.min(clientWidth - CLIENT_WIDTH_OFFSET_SET, canvasRef.width), "px");
    }
  });
  observe.observe(contentWrapper);
  function updateScale() {
    zoomValueRef.val("".concat(scale * 100, "%"));
    $(canvasContainerRef).css('--scale-factor', scale);
  }
  pdfjsLib.getDocument(pdfFile).promise.then(function (pdf) {
    var localPage = pageNum;
    try {
      var localPageData = JSON.parse(localStorage.getItem(LOCAL_STORAGE_PAGE_KEY));
      if (localPageData && localPageData[lesson.lesson_id]) {
        var p = localPageData[lesson.lesson_id];
        localPage = p <= pdf.numPages && p > 1 ? p : pageNum;
      }
    } catch (e) {
      localPage = pageNum;
    }
    pageNum = localPage;
    pdfDoc = pdf;
    totalPagesRef.textContent = pdf.numPages;
    linkService = new pdfjsViewer.PDFLinkService({
      onPageChange: function onPageChange(num) {
        return renderPage(num);
      }
    });
    linkService.setDocument(pdfDoc);
    updateScale();
    renderPage(pageNum);
  });
  function handleNavigation(num) {
    var disabled = 'masterstudy-pdf-btn_disabled';
    backBtn.toggleClass(disabled, num === 1);
    nextBtn.toggleClass(disabled, num === pdfDoc.numPages);
    var onLastPage = num === pdfDoc.numPages;
    if (onLastPage) {
      submitButton.removeAttr('disabled');
      submitButton.removeClass('masterstudy-button_disabled');
      submitHint.css('display', 'none');
    }
  }
  function renderPage(num) {
    pagesInputRef.value = num;
    pageNum = num;
    pdfDoc.getPage(num).then( /*#__PURE__*/function () {
      var _ref = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee(page) {
        var _canvasContainerRef$q;
        var viewport, localData, dpr, transform, clientWidth, renderContext, textContent, annoBuilder;
        return _regeneratorRuntime().wrap(function _callee$(_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              viewport = page.getViewport({
                scale: scale
              });
              dpr = window.devicePixelRatio || 1;
              transform = [dpr, 0, 0, dpr, 0, 0];
              handleNavigation(num);
              try {
                localData = JSON.parse(localStorage.getItem(LOCAL_STORAGE_PAGE_KEY));
                if (localData) {
                  localData[lesson.lesson_id] = num;
                } else {
                  localData = _defineProperty({}, lesson.lesson_id, num);
                }
              } catch (e) {
                localData = _defineProperty({}, lesson.lesson_id, num);
              } finally {
                localStorage.setItem(LOCAL_STORAGE_PAGE_KEY, JSON.stringify(localData));
              }
              if (!initialViewport) {
                initialViewport = viewport;
              }
              clientWidth = contentWrapper.clientWidth - CLIENT_WIDTH_OFFSET_CHECK;
              canvasRef.width = Math.floor(viewport.width * dpr);
              canvasRef.height = Math.floor(viewport.height * dpr);
              canvasRef.style.width = viewport.width + 'px';
              canvasRef.style.height = viewport.height + 'px';
              if (viewport.width < clientWidth) {
                canvasContainerRef.style.width = "".concat(viewport.width, "px");
                canvasContainerRef.style.height = "".concat(viewport.height, "px");
                canvasPosition = {
                  x: 0,
                  y: 0
                };
              } else {
                canvasContainerRef.style.width = "".concat(clientWidth - CLIENT_WIDTH_OFFSET_SET, "px");
                canvasContainerRef.style.height = "".concat(viewport.height, "px");
              }
              renderContext = {
                canvasContext: ctx,
                viewport: viewport,
                transform: transform
              };
              _context.next = 15;
              return page.render(renderContext);
            case 15:
              _context.next = 17;
              return page.getTextContent();
            case 17:
              textContent = _context.sent;
              textLayerDiv.innerHTML = '';
              pdfjsLib.renderTextLayer({
                textContent: textContent,
                container: textLayerDiv,
                viewport: viewport,
                textDivs: []
              });
              (_canvasContainerRef$q = canvasContainerRef.querySelector('.annotationLayer')) === null || _canvasContainerRef$q === void 0 ? void 0 : _canvasContainerRef$q.remove();
              annoBuilder = new pdfjsViewer.AnnotationLayerBuilder({
                pageDiv: canvasContainerRef,
                pdfPage: page,
                linkService: linkService
              });
              _context.next = 24;
              return annoBuilder.render(viewport);
            case 24:
            case "end":
              return _context.stop();
          }
        }, _callee);
      }));
      return function (_x) {
        return _ref.apply(this, arguments);
      };
    }());
  }
  function applyPosition() {
    var maxX = Math.max(0, canvasRef.offsetWidth - canvasContainerRef.clientWidth);
    var maxY = Math.max(0, canvasRef.offsetHeight - canvasContainerRef.clientHeight);
    canvasPosition.x = Math.min(0, Math.max(-maxX, canvasPosition.x));
    canvasPosition.y = Math.min(0, Math.max(-maxY, canvasPosition.y));
    canvasRef.style.transform = "translate(".concat(canvasPosition.x, "px, ").concat(canvasPosition.y, "px)");
    var textLayer = document.getElementById('pdf-text-layer');
    if (textLayer) {
      textLayer.style.transform = "translate(".concat(canvasPosition.x, "px, ").concat(canvasPosition.y, "px)");
    }
  }
  function closeTooltip() {
    toolbarTooltipRef.removeClass('masterstudy-toolbar__menu-tooltip_visible');
    isTooltipVisible = false;
  }
  function createBookmark(listItem, page, title) {
    $.ajax({
      url: lesson.ajax_url,
      dataType: 'json',
      context: this,
      data: {
        'lesson_id': lesson.lesson_id,
        'course_id': lesson.course_id,
        'page_number': page,
        'title': title,
        'action': 'stm_lms_add_bookmark',
        'nonce': lesson.add_bookmark_nonce
      },
      success: function success(response) {
        $(listItem).find('.masterstudy-bookmarks__list-item-page').text(page);
        $(listItem).find('.masterstudy-bookmarks__list-item-title').text(title);
        $(listItem).attr('data-bookmark-id', response.data.bookmark_id);
        $(listItem).removeClass('masterstudy-bookmarks__list-item_editing');
        $('.masterstudy-bookmarks__new-bookmark-container').show();
      }
    });
  }
  function updateBookmark(listItem, id, page, title) {
    $.ajax({
      url: lesson.ajax_url,
      dataType: 'json',
      context: this,
      data: {
        'id': id,
        'page_number': page,
        'title': title,
        'action': 'stm_lms_update_bookmark',
        'nonce': lesson.update_bookmark_nonce
      },
      success: function success() {
        $(listItem).find('.masterstudy-bookmarks__list-item-page').text(page);
        $(listItem).find('.masterstudy-bookmarks__list-item-title').text(title);
        $(listItem).removeClass('masterstudy-bookmarks__list-item_editing');
        $('.masterstudy-bookmarks__new-bookmark-container').show();
      }
    });
  }
  function removeBookmark(listItem, id) {
    var res = confirm('Are you sure you want to delete this bookmark?');
    if (res) {
      $.ajax({
        url: lesson.ajax_url,
        dataType: 'json',
        context: this,
        data: {
          'id': id,
          'action': 'stm_lms_remove_bookmark',
          'nonce': lesson.remove_bookmark_nonce
        },
        success: function success() {
          $(listItem).remove();
        }
      });
    }
  }
  backBtn.on('click', function () {
    if (pageNum <= 1) return;
    pageNum--;
    renderPage(pageNum);
  });
  nextBtn.on('click', function () {
    if (pageNum >= pdfDoc.numPages) return;
    pageNum++;
    renderPage(pageNum);
  });
  pagesInputRef.addEventListener('input', function (e) {
    if (!e.target.value) return;
    var val = parseInt(e.target.value);
    if (Number.isNaN(val) || val > pdfDoc.numPages || val < 1) return;
    pageNum = val;
    renderPage(pageNum);
  });
  zoomInBtnRef.addEventListener('click', function () {
    if (scale >= MAX_ZOOM_SCALE) return;
    scale += 0.25;
    if (canvasContainerRef.offsetWidth >= canvasRef.width) {
      canvasPosition.x = 0;
      canvasPosition.y = 0;
      canvasRef.style.transform = "translate(".concat(canvasPosition.x, "px, ").concat(canvasPosition.y, "px)");
    }
    updateScale();
    renderPage(pageNum);
  });
  zoomOutBtnRef.addEventListener('click', function () {
    if (scale <= MIN_ZOOM_SCALE) return;
    scale -= 0.25;
    canvasPosition.x = 0;
    canvasPosition.y = 0;
    canvasRef.style.transform = "translate(".concat(canvasPosition.x, "px, ").concat(canvasPosition.y, "px)");
    updateScale();
    renderPage(pageNum);
  });
  canvasContainerRef.addEventListener('mousedown', function (e) {
    if (canvasContainerRef.offsetWidth >= canvasRef.width) return;
    isDragging = true;
    dragStart.x = e.clientX - canvasPosition.x;
    dragStart.y = e.clientY - canvasPosition.y;
    canvasContainerRef.style.cursor = 'grabbing';
  });
  canvasContainerRef.addEventListener('touchstart', function (e) {
    if (e.touches.length !== 1 || canvasContainerRef.offsetWidth >= canvasRef.width) return;
    isDragging = true;
    var touch = e.touches[0];
    dragStart.x = touch.clientX - canvasPosition.x;
    dragStart.y = touch.clientY - canvasPosition.y;
  });
  document.addEventListener('mousemove', function (e) {
    if (!isDragging) return;
    canvasPosition.x = e.clientX - dragStart.x;
    canvasPosition.y = e.clientY - dragStart.y;
    applyPosition();
  });
  document.addEventListener('touchmove', function (e) {
    if (!isDragging || e.touches.length !== 1) return;
    var touch = e.touches[0];
    canvasPosition.x = touch.clientX - dragStart.x;
    canvasPosition.y = touch.clientY - dragStart.y;
    applyPosition();
  });
  document.addEventListener('mouseup', function () {
    isDragging = false;
    canvasContainerRef.style.cursor = 'default';
  });
  document.addEventListener('touchend', function (e) {
    if (isDragging && e.touches.length === 0) {
      isDragging = false;
    }
  });
  document.addEventListener('touchcancel', function () {
    if (isDragging) {
      isDragging = false;
    }
  });
  canvasRef.addEventListener('dragstart', function (e) {
    e.preventDefault();
  });
  var downloadPdf = function downloadPdf() {
    var link = document.createElement('a');
    link.href = pdfFile;
    link.download = pdfFileName;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    closeTooltip();
  };
  var printPdf = function printPdf() {
    if (isMobileDevice()) {
      window.open(pdfFile, '_blank');
    } else {
      if (!iframe) {
        iframe = document.createElement('iframe');
        iframe.style.display = "none";
        iframe.src = pdfFile;
        document.body.appendChild(iframe);
      }
      iframe.contentWindow.focus();
      iframe.contentWindow.print();
    }
    closeTooltip();
  };
  var openInNewTab = function openInNewTab() {
    window.open(pdfFile, '_blank');
    closeTooltip();
  };
  toolbarContainer.on('click', '.masterstudy-toolbar__download-btn', downloadPdf);
  toolbarTooltipRef.on('click', '.masterstudy-toolbar__download-btn', downloadPdf);
  toolbarContainer.on('click', '.masterstudy-toolbar__print-btn', printPdf);
  toolbarTooltipRef.on('click', '.masterstudy-toolbar__print-btn', printPdf);
  toolbarContainer.on('click', '.masterstudy-toolbar__open-new-tab-btn', openInNewTab);
  toolbarTooltipRef.on('click', '.masterstudy-toolbar__open-new-tab-btn', openInNewTab);
  toolbarTooltipRef.on('click', '.masterstudy-toolbar__menu-tooltip-overlay', function () {
    console.log('here');
    closeTooltip();
  });
  toolbarTooltipRef.on('click', '.masterstudy-toolbar__close-modal-btn', function () {
    closeTooltip();
  });
  $('.masterstudy-toolbar__menu-btn:visible').on('click', function () {
    if (isTooltipVisible) {
      toolbarTooltipRef.removeClass('masterstudy-toolbar__menu-tooltip_visible');
    } else {
      toolbarTooltipRef.addClass('masterstudy-toolbar__menu-tooltip_visible');
    }
    isTooltipVisible = !isTooltipVisible;
  });
  expandBtnRef.on('click', function () {
    if (isExpanded) {
      scale = IS_MOBILE ? 0.5 : 1;
      expandBtnRef.addClass('masterstudy-toolbar__expand-btn_expanded');
    } else {
      var clientWidth = contentWrapper.clientWidth - CLIENT_WIDTH_OFFSET_CHECK;
      scale = Math.round(clientWidth / initialViewport.width);
      expandBtnRef.removeClass('masterstudy-toolbar__expand-btn_expanded');
    }
    isExpanded = !isExpanded;
    updateScale();
    renderPage(pageNum);
  });
  bookmarksList.on('click', 'li', function () {
    var page = $(this).find('.masterstudy-bookmarks__list-item-page').text();
    if (!page) return;
    page = parseInt(page);
    if (Number.isNaN(page) || page > pdfDoc.numPages || page < 1) return;
    renderPage(page);
  });
  bookmarksList.on('click', 'li .masterstudy-bookmarks__list-item-save', function (e) {
    e.stopPropagation();
    var listItem = $(this).closest('li');
    var id = $(listItem).data('bookmarkId');
    var pageEl = $(listItem).find('.masterstudy-bookmarks__list-item-page__input');
    var titleEl = $(listItem).find('.masterstudy-bookmarks__list-item-title__input');
    var page = pageEl.val();
    var title = titleEl.val();
    if (!page) {
      pageEl.css('border-color', 'var(--danger-100)');
      return;
    }
    if (!title) {
      titleEl.css('border-color', 'var(--danger-100)');
      return;
    }
    page = parseInt(page);
    if (Number.isNaN(page) || page > pdfDoc.numPages || page < 1) {
      pageEl.css('border-color', 'var(--danger-100)');
      return;
    }
    pageEl.css('border-color', '');
    titleEl.css('border-color', '');
    if (id) {
      updateBookmark(listItem, id, page, title);
    } else {
      createBookmark(listItem, page, title);
    }
  });
  bookmarksList.on('click', 'li .masterstudy-bookmarks__list-item-close', function (e) {
    e.stopPropagation();
    var listItem = $(this).closest('li');
    var id = $(listItem).data('bookmarkId');
    if (!id) {
      $(listItem).remove();
      $('.masterstudy-bookmarks__new-bookmark-container').show();
      return;
    }
    var page = $(listItem).find('.masterstudy-bookmarks__list-item-page').text();
    var title = $(listItem).find('.masterstudy-bookmarks__list-item-title').text();
    $(listItem).find('.masterstudy-bookmarks__list-item-page__input').val(page);
    $(listItem).find('.masterstudy-bookmarks__list-item-title__input').val(title);
    $(listItem).removeClass('masterstudy-bookmarks__list-item_editing');
  });
  bookmarksList.on('click', 'li .masterstudy-bookmarks__list-item-delete-btn', function (e) {
    e.stopPropagation();
    var listItem = $(this).closest('li');
    var id = $(listItem).data('bookmarkId');
    if (!id) return;
    removeBookmark(listItem, id);
  });
  bookmarksList.on('click', 'li .masterstudy-bookmarks__list-item-edit-btn', function (e) {
    e.stopPropagation();
    var listItem = $(this).closest('li');
    var id = $(listItem).data('bookmarkId');
    if (!id) return;
    $(listItem).addClass('masterstudy-bookmarks__list-item_editing');
  });
  $('.masterstudy-bookmarks__new-bookmark-btn').on('click', function () {
    $('.masterstudy-bookmarks__new-bookmark-container').hide();
    var newBookmark = "\n            <li class=\"masterstudy-bookmarks__list-item masterstudy-bookmarks__list-item_editing\">\n\t\t\t\t<div class=\"masterstudy-bookmarks__list-item-content\">\n                    <span class=\"masterstudy-bookmarks__list-item-page\">".concat(pageNum, "</span>\n                    <div class=\"masterstudy-bookmarks__list-item-field\">\n                        <span class=\"masterstudy-bookmarks__list-item-field-label\">").concat(translations['page_number'], "</span>\n                        <input class=\"masterstudy-bookmarks__list-item-page__input\" name=\"page\" placeholder=\"").concat(translations['page'], "\" value=\"").concat(pageNum, "\" type=\"number\" max=\"").concat(pdfDoc.numPages, "\" min=\"1\">\n                    </div>\n\n                    <span class=\"masterstudy-bookmarks__list-item-title\"></span>\n                    <div class=\"masterstudy-bookmarks__list-item-field\">\n                        <span class=\"masterstudy-bookmarks__list-item-field-label\">").concat(translations['note'], "</span>\n                        <input class=\"masterstudy-bookmarks__list-item-title__input\" name=\"title\" placeholder=\"").concat(translations['note_placeholder'], "\" value=\"\" type=\"text\">\n                        <button class=\"masterstudy-bookmarks__list-item-save\">\n                            <span>").concat(translations['save'], "</span>\n                        </button>\n                    </div>\n\n                    <div class=\"masterstudy-bookmarks__list-item-actions\">\n                        <button class=\"masterstudy-bookmarks__list-item-close\">\n                            ").concat(translations['cancel'], "\n                        </button>\n                        <button class=\"masterstudy-bookmarks__list-item-edit-btn\">\n                            <span class=\"stmlms-pencil1\"></span>\n                        </button>\n                        <button class=\"masterstudy-bookmarks__list-item-delete-btn\">\n                            <span class=\"stmlms-trash1\"></span>\n                        </button>\n                    </div>\n                </div>\n            </li>\n        ");
    bookmarksList.append(newBookmark);
  });
  $('.masterstudy-bookmarks__collapse-icon').on('click', function () {
    var isOpened = bookmarksList.is(':visible');
    var content = $('.masterstudy-bookmarks-content');
    if (isOpened) {
      content.animate({
        height: 0
      }, 100, function () {
        setTimeout(function () {
          content.css('display', 'none');
          content.css('height', '');
        }, 300);
      });
      $(content).parent().removeClass('masterstudy-bookmarks_opened');
    } else {
      content.css('display', 'block');
      var autoHeight = content.height('auto').height();
      content.height(0).animate({
        height: autoHeight
      }, 100, function () {
        setTimeout(function () {
          content.css('height', '');
        }, 300);
      });
      $(content).parent().addClass('masterstudy-bookmarks_opened');
    }
  });
  $(zoomValueRef).on('change', function () {
    var val = Number($(zoomValueRef).val().replace(/\D/g, ''));
    if (Number.isNaN(val)) {
      updateScale();
      return;
    }
    val = val / 100;
    if (val < MIN_ZOOM_SCALE) {
      val = MIN_ZOOM_SCALE;
    } else if (val > MAX_ZOOM_SCALE) {
      val = MAX_ZOOM_SCALE;
    }
    scale = val;
    updateScale();
    renderPage(pageNum);
  });

  // screen.orientation.addEventListener('change', () => {
  //     renderPage(pageNum)
  // });
})(jQuery);