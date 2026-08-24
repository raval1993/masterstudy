"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, defineProperty = Object.defineProperty || function (obj, key, desc) { obj[key] = desc.value; }, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return defineProperty(generator, "_invoke", { value: makeInvokeMethod(innerFn, self, context) }), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; defineProperty(this, "_invoke", { value: function value(method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; } function maybeInvokeDelegate(delegate, context) { var methodName = context.method, method = delegate.iterator[methodName]; if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel; var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), defineProperty(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (val) { var object = Object(val), keys = []; for (var key in object) keys.push(key); return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
(function ($) {
  $(document).ready(function () {
    var importBtn = $('[data-id="import-students-via-csv"]');
    var importModal = $('.masterstudy-manage-students-import__modal');
    var importModalClose = $('.masterstudy-manage-students-import__modal-close');
    var addBtn = $('[data-id="add-student"]');
    var dropZone = $('.masterstudy-manage-students-import__file-upload');
    var dropZoneField = $('.masterstudy-manage-students-import__file-upload__field');
    var fileInput = $('.masterstudy-manage-students-import__file-upload__input');
    var modalIconWrapper = $('.masterstudy-manage-students-import__adding-box__icon-wrapper');
    var modalIcon = $('.masterstudy-manage-students-import__adding-box__icon');
    var emailInput = $('.masterstudy-manage-students-import__email-input');
    var importedList = $('.masterstudy-manage-students-import__list');
    var courseID = importModal.attr('data-course-id');
    var importProgress = $('.masterstudy-progress');
    var enrolledUsers = [];
    var currentStep = 1;
    var totalUsers = 0;
    var importCounter = 0;
    var userData = [];
    var isManual = false;
    stepHandler(1);
    importBtn.on('click', function (e) {
      e.preventDefault();
      importModal.addClass('is-open');
    });
    $(window).on('click', function (event) {
      if ($(event.target).is(importModal)) {
        importModal.removeClass('is-open');
        resetAll();
      }
    });
    importModalClose.on('click', function (e) {
      e.preventDefault();
      importModal.removeClass('is-open');
      resetAll();
    });
    dropZone.on('dragover', function (e) {
      e.preventDefault();
      e.stopPropagation();
      dropZoneField.addClass('highlight');
    });
    dropZone.on('dragleave', function (e) {
      e.preventDefault();
      e.stopPropagation();
      dropZoneField.removeClass('highlight');
    });
    dropZone.on('drop', function (e) {
      e.preventDefault();
      e.stopPropagation();
      dropZoneField.removeClass('highlight');
      fileUploadHandler(e.originalEvent.dataTransfer.files);
    });
    $('[data-id="import-students-upload-csv-btn"]').on('click', function () {
      fileInput.click();
    });
    $('.masterstudy-manage-students-import__file-attachment__delete').on('click', function () {
      fileInput.val('');
      stepHandler(1);
    });
    $('[data-id="import-students-close-modal"]').on('click', function () {
      importModal.removeClass('is-open');
      resetAll();
    });
    $('[data-id="import-students-next-attempt"]').on('click', function () {
      fileInput.val('');
      stepHandler(1);
      dropZoneField.removeClass('error');
      $('.masterstudy-manage-students-import__unsupported-file-type').addClass('hidden');
      modalIconWrapper.removeClass('error');
    });
    fileInput.on('change', function (event) {
      fileUploadHandler(event.target.files);
    });
    $('[data-id="import-students-submit"]').on('click', /*#__PURE__*/function () {
      var _ref = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee(e) {
        return _regeneratorRuntime().wrap(function _callee$(_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              e.preventDefault();
              if (!(1 === currentStep)) {
                _context.next = 3;
                break;
              }
              return _context.abrupt("return");
            case 3:
              stepHandler(3);
              _context.prev = 4;
              _context.next = 7;
              return importInChunks(userData, 50);
            case 7:
              _context.next = 14;
              break;
            case 9:
              _context.prev = 9;
              _context.t0 = _context["catch"](4);
              stepHandler(5);
              modalIconWrapper.addClass('error');
              console.error(_context.t0);
            case 14:
            case "end":
              return _context.stop();
          }
        }, _callee, null, [[4, 9]]);
      }));
      return function (_x) {
        return _ref.apply(this, arguments);
      };
    }());
    addBtn.on('click', function (e) {
      e.preventDefault();
      stepHandler(7);
      totalUsers = 1;
      importCounter = 0;
      isManual = true;
      modalIcon.addClass('envelope');
      importModal.addClass('is-open');
    });
    $('[data-id="send-invitation"]').on('click', function (e) {
      e.preventDefault();
      var email = emailInput.val();
      if (!validateEmail(email)) {
        $('.masterstudy-manage-students-import__incorrect-email').removeClass('hidden');
        return;
      }
      $(this).addClass('masterstudy-button_loading');
      addStudent({
        email: email
      });
    });
    emailInput.on('change paste keyup', function () {
      $('.masterstudy-manage-students-import__incorrect-email').addClass('hidden');
    });
    function fileUploadHandler(files) {
      if (files === undefined || files === null) return;
      var file = files[0];
      if (file === undefined || file === null) return;
      var fileExtension = file.name.split('.').pop().toLowerCase();
      if (!['csv'].includes(fileExtension)) {
        dropZoneField.addClass('error');
        $('.masterstudy-manage-students-import__unsupported-file-type').removeClass('hidden');
        return;
      }
      $('.masterstudy-manage-students-import__file-attachment__title').text(file.name);
      $('.masterstudy-manage-students-import__file-attachment__size').text(getFileSize(file.size));
      readCSVFile(file).then(function (data) {
        var headers = ['email'];
        var csvHeaders = Object.keys(data[0] || []);
        var isValidCSV = headers.every(function (header) {
          return csvHeaders.includes(header);
        });
        if (!isValidCSV && csvHeaders.length) {
          isValidCSV = headers.every(function (header) {
            return csvHeaders[0].split(';').includes(header);
          });
        }
        if (isValidCSV && csvHeaders.length > 0 && data.length > 0) {
          totalUsers = data.length;
          userData = data;
          stepHandler(2);
        } else {
          dropZoneField.addClass('error');
          $('.masterstudy-manage-students-empty-file').removeClass('hidden');
        }
      })["catch"](function (error) {
        console.error('Error reading CSV file:', error);
      });
    }
    function importInChunks(_x2) {
      return _importInChunks.apply(this, arguments);
    }
    function _importInChunks() {
      _importInChunks = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee2(users) {
        var chunkSize,
          i,
          chunk,
          apiUrl,
          res,
          data,
          processed,
          progress,
          _args2 = arguments;
        return _regeneratorRuntime().wrap(function _callee2$(_context2) {
          while (1) switch (_context2.prev = _context2.next) {
            case 0:
              chunkSize = _args2.length > 1 && _args2[1] !== undefined ? _args2[1] : 50;
              totalUsers = users.length;
              importCounter = 0;
              enrolledUsers = [];
              i = 0;
            case 5:
              if (!(i < users.length)) {
                _context2.next = 25;
                break;
              }
              chunk = users.slice(i, i + chunkSize);
              apiUrl = "".concat(ms_lms_resturl, "/student/bulk/").concat(courseID, "/");
              _context2.next = 10;
              return fetch(apiUrl, {
                method: 'POST',
                headers: {
                  'X-WP-Nonce': ms_lms_nonce,
                  'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                  course_id: Number(courseID),
                  students: chunk
                })
              });
            case 10:
              res = _context2.sent;
              if (res.ok) {
                _context2.next = 13;
                break;
              }
              throw new Error("Bulk import failed: HTTP ".concat(res.status));
            case 13:
              _context2.next = 15;
              return res.json();
            case 15:
              data = _context2.sent;
              processed = Array.isArray(data.added) ? data.added.length : chunk.length;
              importCounter += processed;
              progress = Math.round(importCounter / totalUsers * 100);
              importProgress.find('.masterstudy-progress__bar-filled').css({
                width: "".concat(progress, "%")
              });
              importProgress.find('.masterstudy-progress__percent').text(progress);
              (data.added || []).forEach(function (row) {
                if (row && row.is_enrolled_before) enrolledUsers.push(row);
              });
            case 22:
              i += chunkSize;
              _context2.next = 5;
              break;
            case 25:
              setAddStudentEvent(100);
              afterImportHandler();
            case 27:
            case "end":
              return _context2.stop();
          }
        }, _callee2);
      }));
      return _importInChunks.apply(this, arguments);
    }
    function readCSVFile(file) {
      totalUsers = 0;
      importCounter = 0;
      userData = {};
      return new Promise(function (resolve, reject) {
        var reader = new FileReader();
        reader.readAsText(file, 'UTF-8');
        reader.onload = function (event) {
          var csvData = event.target.result;
          var lines = csvData.split('\n');
          var headers = lines[0].trim().split(',').map(function (header) {
            return header.trim();
          });
          var dataArray = [];
          for (var i = 1; i < lines.length; i++) {
            var values = lines[i].trim().split(',').map(function (value) {
              return value.trim();
            });
            var obj = {};
            for (var j = 0; j < headers.length; j++) {
              var key = headers[j];
              if (values[j] !== undefined && values[j] !== '') {
                obj[key] = values[j];
              }
            }
            if (Object.keys(obj).length > 0) {
              dataArray.push(obj);
            }
          }
          resolve(dataArray);
        };
        reader.onerror = function (error) {
          reject(error);
        };
      });
    }
    function resetAll() {
      fileInput.val('');
      emailInput.val('');
      stepHandler(1);
      isManual = false;
      enrolledUsers = [];
      dropZoneField.removeClass('error');
      modalIcon.removeClass('envelope');
      $('.masterstudy-manage-students-empty-file').addClass('hidden');
      $('.masterstudy-button').removeClass('masterstudy-button_loading');
      $('.masterstudy-manage-students-import__unsupported-file-type').addClass('hidden');
      modalIconWrapper.removeClass('error');
      importedList.html('');
    }
    function stepHandler(step) {
      var stepItems = importModal.find('[data-step]');
      currentStep = step;
      stepItems.each(function (i, item) {
        var itemSteps = $(item).attr('data-step');
        var dataSteps = itemSteps.split(',').map(function (item) {
          return parseInt(item, 10);
        });
        if (dataSteps.indexOf(step) !== -1) {
          $(item).removeClass('hidden');
        } else {
          $(item).addClass('hidden');
        }
        if (1 === step) {
          fileInput.val('');
          $('[data-id="import-students-submit"]').addClass('masterstudy-button_disabled');
        } else {
          $('[data-id="import-students-submit"]').removeClass('masterstudy-button_disabled');
        }
      });
    }
    function getFileSize(bytes) {
      var KB = 1024;
      var MB = KB * 1024;
      var GB = MB * 1024;
      if (bytes >= GB) {
        return (bytes / GB).toFixed(2) + ' gb';
      } else if (bytes >= MB) {
        return (bytes / MB).toFixed(2) + ' mb';
      } else if (bytes >= KB || bytes / KB < 1) {
        return (bytes / KB).toFixed(2) + ' kb';
      } else {
        return bytes + ' bytes';
      }
    }
    function addStudent() {
      var params = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var queryString = new URLSearchParams(params).toString();
      var apiUrl = "".concat(ms_lms_resturl, "/student/").concat(courseID, "/?").concat(queryString);
      fetch(apiUrl, {
        method: 'POST',
        headers: {
          'X-WP-Nonce': ms_lms_nonce,
          'Content-Type': 'application/json'
        }
      }).then(function (response) {
        if (response.ok) {
          return response.json();
        }
      }).then(function (data) {
        if (data) {
          importCounter += 1;
          var progress = Math.round(importCounter / totalUsers * 100, 0);
          importProgress.find('.masterstudy-progress__bar-filled').css({
            width: "".concat(progress, "%")
          });
          importProgress.find('.masterstudy-progress__percent').text(progress);
          if (data.is_enrolled_before) {
            enrolledUsers.push(data);
          }
          if (importCounter === totalUsers) {
            setTimeout(function () {
              setAddStudentEvent(progress);
              afterImportHandler();
            }, 1500);
          }
        } else {
          stepHandler(5);
          modalIconWrapper.addClass('error');
        }
      })["catch"](function (error) {
        stepHandler(5);
        modalIconWrapper.addClass('error');
      });
    }
    function afterImportHandler() {
      modalIcon.removeClass('envelope');
      if (isManual) {
        stepHandler(8);
        isManual = false;
        return;
      }
      stepHandler(4);
      $('.masterstudy-manage-students-import__user-count').text(totalUsers);
      if (enrolledUsers.length) {
        $('.masterstudy-manage-students-import__user-count').text(totalUsers - enrolledUsers.length);
        enrolledUsers.map(function (user) {
          importedList.append("<span class=\"masterstudy-manage-students-import__list-item\">".concat(user.email, "</span>"));
        });
        stepHandler(6);
      }
    }
    function setAddStudentEvent(progress) {
      document.dispatchEvent(new CustomEvent('msAddStudentEvent', {
        detail: {
          progress: progress
        }
      }));
    }
    function validateEmail(email) {
      var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    }
  });
})(jQuery);