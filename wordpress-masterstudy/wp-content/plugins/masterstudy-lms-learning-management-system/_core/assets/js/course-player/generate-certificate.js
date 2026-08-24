"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, defineProperty = Object.defineProperty || function (obj, key, desc) { obj[key] = desc.value; }, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return defineProperty(generator, "_invoke", { value: makeInvokeMethod(innerFn, self, context) }), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; defineProperty(this, "_invoke", { value: function value(method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; } function maybeInvokeDelegate(delegate, context) { var methodName = context.method, method = delegate.iterator[methodName]; if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel; var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), defineProperty(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (val) { var object = Object(val), keys = []; for (var key in object) keys.push(key); return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
var jsPDF = window.jspdf.jsPDF;
(function ($) {
  var lastClickElClass = null;
  $(document).ready(function () {
    if (course_certificate.preview && !course_certificate.demo) {
      getCertificate(course_certificate.course_id, course_certificate.course_id, true);
    }
    $('body').on('click', '.masterstudy-single-course-complete__buttons .masterstudy-certificate-btn, .masterstudy_preview_certificate, .masterstudy-student-course-card .masterstudy-button', function (e) {
      e.preventDefault();
      if (course_certificate.demo) return;
      var courseId = $(this).attr('data-id') || false;
      lastClickElClass = $(this).attr('class');
      if (courseId) {
        getCertificate(courseId, courseId);
      }
    });
  });

  //Get Certificate Data
  function getCertificate(id) {
    var courseId = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
    var preview = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
    var params = new URLSearchParams({
      action: 'stm_get_certificate',
      nonce: course_certificate.nonce,
      post_id: id,
      course_id: courseId
    });
    if (course_certificate.user_id) {
      params.append('user_id', course_certificate.user_id);
    }
    if (preview) {
      $('.masterstudy-page-certificate__download .masterstudy-button').addClass('masterstudy-button_loading');
    }
    $.ajax({
      url: "".concat(course_certificate.ajax_url, "?").concat(params.toString()),
      method: 'GET',
      success: function success(response) {
        if (response !== null && response !== void 0 && response.data) {
          renderCertificate(response.data, preview);
        }
      }
    });
  }

  //Convert HTML Canvas to Image
  function renderCertificate(data, preview) {
    var fonts = new Set();
    var container = document.createElement('div');
    container.classList.add('certificate-preview');
    var orientation = data.orientation;
    var bgImage = document.createElement('img');
    bgImage.src = data.thumbnail;
    bgImage.style.width = '100%';
    bgImage.style.height = '100%';
    bgImage.style.position = 'absolute';
    bgImage.style.top = '0';
    bgImage.style.left = '0';
    container.style.width = data.orientation === 'portrait' ? '636px' : '898px';
    container.style.height = data.orientation === 'portrait' ? '898px' : '636px';
    container.style.lineHeight = '1.4';
    container.style.position = 'relative';
    container.appendChild(bgImage);
    var qrPromises = [];
    data.fields.forEach(function (field) {
      var element;
      if (field.type === 'image') {
        element = document.createElement('img');
        element.src = field.content;
        element.style.width = field.w + "px";
        element.style.height = field.h + "px";
      } else if (field.type === 'qrcode') {
        var tempDiv = document.createElement('div');
        document.body.appendChild(tempDiv);
        qrPromises.push(new Promise(function (resolve) {
          new QRCode(tempDiv, {
            text: field.content,
            width: field.w,
            height: field.h,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
          });
          setTimeout(function () {
            var qrCanvas = tempDiv.querySelector('canvas');
            if (qrCanvas) {
              var qrImage = document.createElement('img');
              qrImage.src = qrCanvas.toDataURL("image/png");
              qrImage.style.width = field.w + 'px';
              qrImage.style.height = field.h + 'px';
              qrImage.style.position = 'absolute';
              qrImage.style.left = field.x + 'px';
              qrImage.style.top = field.y + 'px';
              container.appendChild(qrImage);
            }
            document.body.removeChild(tempDiv);
            resolve();
          }, 100);
        }));
        return;
      } else if (field.type === 'shape') {
        var shapeId = field.content ? parseInt(field.content) : 1;
        var shapeData = course_certificate.shapes.find(function (shape) {
          return shape.id === shapeId;
        });
        if (shapeData && shapeData.svg) {
          var parser = new DOMParser();
          var svgDoc = parser.parseFromString(shapeData.svg, "image/svg+xml");
          var svgElement = svgDoc.documentElement;
          if (field.styles && field.styles.color) {
            var color = field.styles.color.hex || '#000000';
            svgElement.querySelectorAll('*').forEach(function (el) {
              if (el.hasAttribute('fill')) {
                el.setAttribute('fill', color);
              }
              if (el.hasAttribute('stroke')) {
                el.setAttribute('stroke', color);
              }
            });
          }
          var scale = 2;
          var imgWidth = field.w * scale;
          var imgHeight = field.h * scale;
          svgElement.setAttribute("width", imgWidth);
          svgElement.setAttribute("height", imgHeight);
          var serializer = new XMLSerializer();
          var svgString = serializer.serializeToString(svgElement);
          var encodedData = "data:image/svg+xml;base64," + btoa(unescape(encodeURIComponent(svgString)));
          var svgImage = document.createElement('img');
          svgImage.src = encodedData;
          svgImage.style.width = field.w + 'px';
          svgImage.style.height = field.h + 'px';
          svgImage.style.position = 'absolute';
          svgImage.style.left = field.x + 'px';
          svgImage.style.top = field.y + 'px';
          container.appendChild(svgImage);
        }
        return;
      } else {
        element = document.createElement('div');
        if (field.type === 'grades') {
          element.innerHTML = renderGradesHtml(field.content).prop('outerHTML');
        } else {
          element.textContent = field.content;
        }
        element.style.fontSize = field.styles.fontSize || '16px';
        element.style.color = field.styles.color.hex || '#000';
        element.style.textAlign = field.styles.textAlign || 'left';
        element.style.fontFamily = field.styles.fontFamily || 'Arial';
        element.style.fontWeight = field.styles.fontWeight ? 'bold' : 'normal';
        element.style.fontStyle = field.styles.fontStyle ? 'italic' : 'normal';
        element.style.width = field.w + 'px';
      }
      if (field.styles && field.styles.fontFamily) {
        fonts.add(field.styles.fontFamily);
      }
      element.style.position = 'absolute';
      element.style.left = field.x + 'px';
      element.style.top = field.y + 'px';
      container.appendChild(element);
    });
    var uniqueFonts = Array.from(fonts);
    injectGoogleFonts(uniqueFonts);
    var contentBlock = document.querySelector('.masterstudy-course-player-content');
    if (contentBlock) {
      contentBlock.after(container);
    } else {
      document.body.appendChild(container);
    }
    container.style.position = "fixed";
    container.style.top = "100%";
    setTimeout(function () {
      convertToPDF(container, preview, orientation);
    }, 500);
  }

  //Get config Google Fonts
  function loadFonts() {
    return _loadFonts.apply(this, arguments);
  } //Get API Google Fonts
  function _loadFonts() {
    _loadFonts = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
      var jsonPath, response, data;
      return _regeneratorRuntime().wrap(function _callee$(_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            jsonPath = course_certificate.googleFonts;
            _context.prev = 1;
            _context.next = 4;
            return fetch(jsonPath);
          case 4:
            response = _context.sent;
            if (response.ok) {
              _context.next = 7;
              break;
            }
            throw new Error("File not found");
          case 7:
            _context.next = 9;
            return response.json();
          case 9:
            data = _context.sent;
            if (!Array.isArray(data)) {
              _context.next = 12;
              break;
            }
            return _context.abrupt("return", data);
          case 12:
            _context.next = 17;
            break;
          case 14:
            _context.prev = 14;
            _context.t0 = _context["catch"](1);
            console.warn("Google fonts list could not be loaded:", _context.t0);
          case 17:
            return _context.abrupt("return", []);
          case 18:
          case "end":
            return _context.stop();
        }
      }, _callee, null, [[1, 14]]);
    }));
    return _loadFonts.apply(this, arguments);
  }
  function injectGoogleFonts(_x) {
    return _injectGoogleFonts.apply(this, arguments);
  }
  function _injectGoogleFonts() {
    _injectGoogleFonts = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee2(fontsList) {
      var fontsToLoad, fontUrl, existingLink, link;
      return _regeneratorRuntime().wrap(function _callee2$(_context2) {
        while (1) switch (_context2.prev = _context2.next) {
          case 0:
            if (!(!fontsList || fontsList.length === 0)) {
              _context2.next = 2;
              break;
            }
            return _context2.abrupt("return");
          case 2:
            if (injectGoogleFonts.fonts) {
              _context2.next = 6;
              break;
            }
            _context2.next = 5;
            return loadFonts();
          case 5:
            injectGoogleFonts.fonts = _context2.sent;
          case 6:
            fontsToLoad = new Set();
            fontsList.forEach(function (font) {
              var fontData = injectGoogleFonts.fonts.find(function (f) {
                return f.family === font;
              });
              if (!fontData) {
                return;
              }
              var weights = ["400", "400italic", "700", "700italic"];
              var baseFont = encodeURIComponent(font);
              var fontStr = "".concat(baseFont, ":").concat(weights.join(","));
              fontsToLoad.add(fontStr);
            });
            if (fontsToLoad.size > 0) {
              fontUrl = "https://fonts.googleapis.com/css?family=".concat(Array.from(fontsToLoad).join("%7C"), "&display=swap");
              existingLink = document.getElementById("google-fonts-link");
              if (existingLink) {
                existingLink.href = fontUrl;
              } else {
                link = document.createElement("link");
                link.id = "google-fonts-link";
                link.rel = "stylesheet";
                link.href = fontUrl;
                link.media = "all";
                document.head.appendChild(link);
              }
            }
          case 9:
          case "end":
            return _context2.stop();
        }
      }, _callee2);
    }));
    return _injectGoogleFonts.apply(this, arguments);
  }
  function renderGradesHtml(content) {
    var $container = $('<div class="masterstudy-grades-certificate"></div>');
    var $main = $("\n            <div class=\"masterstudy-grades-certificate__main\">\n            <div class=\"masterstudy-grades-certificate__badge\">\n                <div class=\"masterstudy-grades-certificate__badge-value\">".concat(content.grade.badge, "</div>\n                <div class=\"masterstudy-grades-certificate__badge-label\">").concat(content.main_data.grade_title, "</div>\n            </div>\n            <div class=\"masterstudy-grades-certificate__points\">\n                <div class=\"masterstudy-grades-certificate__points-value\">").concat(content.grade.current, "</div>\n                <div class=\"masterstudy-grades-certificate__points-label\">").concat(content.main_data.point_title, "</div>\n            </div>\n            <div class=\"masterstudy-grades-certificate__range\">\n                <div class=\"masterstudy-grades-certificate__range-value\">").concat(content.grade.range, "%</div>\n                <div class=\"masterstudy-grades-certificate__range-label\">").concat(content.main_data.range_title, "</div>\n            </div>\n            </div>\n        "));
    var $examsSection = $("\n            <div class=\"masterstudy-grades-certificate__exams\">\n            <div class=\"masterstudy-grades-certificate__exams-title\">".concat(content.main_data.exams_title, ":</div>\n            <table class=\"masterstudy-grades-certificate__exams-table\">\n                <tbody></tbody>\n            </table>\n            </div>\n        "));
    var $tbody = $examsSection.find('tbody');
    content.exams.forEach(function (exam) {
      var $row = $("<tr class=\"masterstudy-grades-certificate__exams-row masterstudy-grades-certificate__exams-row-".concat(exam.type, "\"></tr>"));
      var $labelCell = $("\n            <td class=\"masterstudy-grades-certificate__exams-label\">\n                <div class=\"masterstudy-grades-certificate__exams-icon\"></div>\n                ".concat(exam.title, "\n            </td>\n            "));
      $row.append($labelCell);
      if (exam.attempts < 1) {
        var $notStarted = $("\n                <td colspan=\"3\" class=\"masterstudy-grades-certificate__exams-not-started\">".concat(content.main_data.attempt_title, "</td>\n            "));
        $row.append($notStarted);
      } else {
        var $grade = $("\n                <td class=\"masterstudy-grades-certificate__exams-grade\">\n                <span class=\"masterstudy-grades-certificate__exams-badge\" style=\"background:".concat(exam.grade.color, "\">\n                    ").concat(exam.grade.badge, "\n                </span>\n                </td>\n            "));
        var $value = $("\n                <td class=\"masterstudy-grades-certificate__exams-value\">\n                ".concat(exam.grade.current).concat(content.main_data.separator).concat(exam.grade.max_point, "\n                </td>\n            "));
        var $percent = $("\n                <td class=\"masterstudy-grades-certificate__exams-percent\">\n                ".concat(exam.grade.range, "%\n                </td>\n            "));
        $row.append($grade, $value, $percent);
      }
      $tbody.append($row);
    });
    $container.append($main, $examsSection);
    return $container;
  }

  //Convert image to PDF
  function convertToPDF(element, preview, orientation) {
    var changedLang = null;
    if (document.documentElement.lang === 'bg-BG') {
      changedLang = document.documentElement.lang;
      document.documentElement.lang = 'en-US';
    }
    html2canvas(element, {
      scale: 3,
      useCORS: true
    }).then(function (canvas) {
      var imgData = canvas.toDataURL('image/jpeg', 1.0);
      var isPortrait = orientation === 'portrait';
      var pdfWidth = isPortrait ? 794 : 1123;
      var pdfHeight = isPortrait ? 1123 : 794;
      var doc = new jsPDF({
        orientation: orientation,
        unit: 'px',
        format: [pdfWidth, pdfHeight]
      });
      doc.addImage(imgData, 'JPEG', 0, 0, pdfWidth, pdfHeight);
      var pdfBlob = doc.output('blob');
      var pdfUrl = URL.createObjectURL(pdfBlob);
      if (preview) {
        $('.masterstudy-page-certificate__preview').removeClass('masterstudy-page-certificate__preview_empty').html('<img src="' + imgData + '" alt="Certificate Preview">');
        $('.masterstudy-page-certificate__download .masterstudy-button').attr('href', doc.output('bloburl'));
        $('.masterstudy-page-certificate__download .masterstudy-button').removeClass('masterstudy-button_loading');
      } else {
        if (course_certificate.emit_pdf_url) {
          document.dispatchEvent(new CustomEvent('generatedCertificateUrl', {
            detail: {
              value: pdfUrl,
              className: lastClickElClass
            }
          }));
        } else {
          window.open(pdfUrl, '_blank');
        }
      }
      element.remove();
    })["finally"](function () {
      if (changedLang) {
        document.documentElement.lang = changedLang;
      }
    });
  }
})(jQuery);