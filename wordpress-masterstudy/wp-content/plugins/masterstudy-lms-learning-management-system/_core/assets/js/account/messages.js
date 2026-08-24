"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, defineProperty = Object.defineProperty || function (obj, key, desc) { obj[key] = desc.value; }, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return defineProperty(generator, "_invoke", { value: makeInvokeMethod(innerFn, self, context) }), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; defineProperty(this, "_invoke", { value: function value(method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; } function maybeInvokeDelegate(delegate, context) { var methodName = context.method, method = delegate.iterator[methodName]; if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel; var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), defineProperty(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (val) { var object = Object(val), keys = []; for (var key in object) keys.push(key); return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
(function ($) {
  $(document).ready(function () {
    var $root = $(".masterstudy-account-messages");
    if (!$root.length) return;
    var $loader = $root.find(".masterstudy-account-messages__loader");
    var $empty = $root.find(".masterstudy-account-messages__empty");
    var $conversationsContainer = $root.find(".masterstudy-account-messages__conversations");
    var $chatContainer = $root.find(".masterstudy-account-messages__chat-container");
    var $listContainer = $root.find(".masterstudy-account-messages__list");
    var $panelAvatar = $root.find(".masterstudy-account-messages__panel-avatar");
    var $panelName = $root.find(".masterstudy-account-messages__panel-name");
    var $panelTime = $root.find(".masterstudy-account-messages__panel-time");
    var $refreshBtn = $root.find(".masterstudy-account-messages__panel-action--refresh .stmlms-sync");
    var $profileBtn = $root.find(".masterstudy-account-messages__panel-action--profile");
    var $chatMessages = $root.find(".masterstudy-account-messages_chat");
    var $sendMessage = $root.find(".masterstudy-account-messages_chat__send-message");
    var $sendBtn = $root.find(".masterstudy-account-messages_chat__send-btn");
    var $sendResponse = $root.find(".masterstudy-account-messages_chat__send-response");
    var conversations = [];
    var conversationId = 0;
    function toBool(val) {
      return val === true || val === 1 || val === "1" || val === "true";
    }
    var instructorPublic = toBool(chat_data.instructor_public);
    var studentPublic = toBool(chat_data.student_public);
    var user_id = chat_data.user_id;
    var myMessage = "";
    var DESKTOP_MIN = 1024;
    var MESSAGE_MIN_HEIGHT = 35;
    var MESSAGE_MAX_HEIGHT = 300;
    function normalizeMessage(val) {
      return String(val || '').replace(/\s+/g, ' ').trim();
    }
    function updateSendState() {
      var canSend = normalizeMessage(myMessage).length > 0;
      $sendBtn.prop('disabled', !canSend);
      $sendBtn.toggleClass('masterstudy-account-messages_chat__send-btn_disabled', !canSend);
    }
    function resizeMessageInput() {
      var textarea = $sendMessage.get(0);
      if (!textarea) return;
      textarea.style.height = "".concat(MESSAGE_MIN_HEIGHT, "px");
      var nextHeight = Math.min(textarea.scrollHeight, MESSAGE_MAX_HEIGHT);
      textarea.style.height = "".concat(nextHeight, "px");
      textarea.style.overflowY = textarea.scrollHeight > MESSAGE_MAX_HEIGHT ? "auto" : "hidden";
    }
    function isDesktop() {
      return window.matchMedia("(min-width: ".concat(DESKTOP_MIN, "px)")).matches;
    }
    function showChatOnMobile() {
      if (isDesktop()) return;
      $chatContainer.removeClass("is-hidden");
    }
    function showListOnMobile() {
      if (isDesktop()) return;
      $chatContainer.addClass("is-hidden");
    }
    function setIsUpdating(val) {
      $refreshBtn.toggleClass("active", !!val);
    }
    function setIsLoading(val) {
      $sendBtn.toggleClass("masterstudy-account-messages_chat__send-btn_loading", !!val);
    }
    function setResponse(val) {
      $sendResponse.toggle(!!val);
      $sendResponse.text(val || "");
    }
    function scrollMessagesBottom() {
      setIsUpdating(false);
      var el = document.getElementById("masterstudy-account-messages_chat");
      if (!el) return;
      el.scrollTop = el.scrollHeight;
    }
    function isProfileAllowed(conversation) {
      var _conversation$compani;
      var isInstructor = toBool(conversation === null || conversation === void 0 || (_conversation$compani = conversation.companion) === null || _conversation$compani === void 0 ? void 0 : _conversation$compani.is_instructor);
      return instructorPublic && isInstructor || studentPublic && !isInstructor;
    }
    function renderConversationItem(val, idx) {
      var isActive = conversationId === idx;
      var isFromMe = val["conversation_info"]["user_form"] === user_id;
      var newCount = isFromMe ? val["conversation_info"]["uf_new_messages"] || 0 : val["conversation_info"]["ut_new_messages"] || 0;
      var allCount = val["conversation_info"]["messages_number"] || 0;
      var $item = $("<span>", {
        "class": "masterstudy-account-messages__list-item ".concat(isActive ? "is-active" : "")
      });
      $item.attr("data-conversation-id", idx);
      $item.append("\n        <div class=\"masterstudy-account-messages__list-avatar\">".concat(val["companion"]["avatar"] || "", "</div>\n        <div class=\"masterstudy-account-messages__list-body\">\n          <div class=\"masterstudy-account-messages__list-name\">").concat(val["companion"]["login"] || "", "</div>\n          <div class=\"masterstudy-account-messages__list-time\">").concat(val["conversation_info"]["ago"] || "", "</div>\n        </div>\n        ").concat(newCount > 0 ? "<div class=\"masterstudy-account-messages__list-badge has_new\">".concat(newCount, "</div>") : "", "\n      "));
      $item.on("click", /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
        var oldId, url;
        return _regeneratorRuntime().wrap(function _callee$(_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              oldId = conversationId;
              conversationId = idx;

              // reset local counters
              ["uf_new_messages", "ut_new_messages"].forEach(function (key) {
                var _conversations$idx;
                if (((_conversations$idx = conversations[idx]) === null || _conversations$idx === void 0 || (_conversations$idx = _conversations$idx.conversation_info) === null || _conversations$idx === void 0 ? void 0 : _conversations$idx[key]) > 0) conversations[idx].conversation_info[key] = 0;
              });
              url = stm_lms_ajaxurl + "?action=stm_lms_clear_new_messages&nonce=" + stm_lms_nonces["stm_lms_clear_new_messages"] + "&conversation_id=" + conversations[conversationId]["conversation_info"]["conversation_id"];
              _context.prev = 4;
              fetch(url, {
                method: "GET"
              });
              renderChatHeader();
              showChatOnMobile();
              scrollToChatPanel();
              _context.next = 11;
              return getMessages(conversations[conversationId]["conversation_info"]["conversation_id"], false, true);
            case 11:
              $(".masterstudy-account-messages__list-item[data-conversation-id=\"".concat(oldId, "\"]")).removeClass("is-active");
              $(this).addClass("is-active");
              _context.next = 19;
              break;
            case 15:
              _context.prev = 15;
              _context.t0 = _context["catch"](4);
              console.error(_context.t0);
              scrollMessagesBottom();
            case 19:
            case "end":
              return _context.stop();
          }
        }, _callee, this, [[4, 15]]);
      })));
      return $item;
    }
    $root.on("click", '[data-id="masterstudy-account-messages-back"]', function (e) {
      e.preventDefault();
      showListOnMobile();
    });
    function renderConversations() {
      $conversationsContainer.empty();
      if (!conversations.length) {
        return;
      }
      conversations.forEach(function (c, idx) {
        $conversationsContainer.append(renderConversationItem(c, idx));
      });
    }
    function renderChatHeader() {
      var conversation = conversations[conversationId];
      if (!conversation) return;
      $panelAvatar.html(conversation["companion"]["avatar"] || "");
      $panelName.text(conversation["companion"]["login"] || "");
      $panelTime.text(conversation["conversation_info"]["ago"] || "");
      var allowed = isProfileAllowed(conversation);
      var url = allowed ? conversation["companion"]["url"] || "#" : "#";
      $panelName.attr("href", url);
      $panelName.toggleClass("is-disabled", !allowed);
      $profileBtn.attr("href", url);
      $profileBtn.css("display", allowed ? "inline-flex" : "none");
    }
    function renderMessages() {
      var conversation = conversations[conversationId];
      if (!conversation || !conversation.messages) return;
      $chatMessages.empty();
      conversation.messages.forEach(function (message) {
        var _message$companion;
        var isOwner = !!message.isOwner;
        var who = isOwner ? chat_data.you || "You" : (message === null || message === void 0 || (_message$companion = message.companion) === null || _message$companion === void 0 ? void 0 : _message$companion.login) || "User";
        var ago = message.ago || "";
        $chatMessages.append("\n          <div class=\"masterstudy-account-messages__message ".concat(isOwner ? "masterstudy-account-messages__message--out" : "masterstudy-account-messages__message--in", "\">\n            <div class=\"masterstudy-account-messages__bubble ").concat(isOwner ? "masterstudy-account-messages__bubble--out" : "", "\">\n              ").concat(message.message, "\n              <div class=\"masterstudy-account-messages__meta\">\n                <span>").concat(who, "</span><span>").concat(ago, "</span>\n              </div>\n            </div>\n          </div>\n        "));
      });
    }
    function getConversations() {
      return _getConversations.apply(this, arguments);
    }
    function _getConversations() {
      _getConversations = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee3() {
        var url, res, response;
        return _regeneratorRuntime().wrap(function _callee3$(_context3) {
          while (1) switch (_context3.prev = _context3.next) {
            case 0:
              url = stm_lms_ajaxurl + "?action=stm_lms_get_user_conversations&nonce=" + stm_lms_nonces["stm_lms_get_user_conversations"];
              showLoader();
              hideEmpty();
              _context3.prev = 3;
              _context3.next = 6;
              return fetch(url, {
                method: "GET"
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
              response = _context3.sent;
              conversations.length = 0;
              (response || []).forEach(function (val) {
                return conversations.push(val);
              });
              renderConversations();
              if (!conversations.length) {
                _context3.next = 24;
                break;
              }
              $listContainer.removeClass("is-hidden");
              if (isDesktop()) {
                $chatContainer.removeClass("is-hidden");
              } else {
                $chatContainer.addClass("is-hidden");
              }
              renderChatHeader();
              hideLoader();
              _context3.next = 22;
              return getMessages(conversations[conversationId]["conversation_info"]["conversation_id"], false, true);
            case 22:
              _context3.next = 25;
              break;
            case 24:
              showEmpty();
            case 25:
              _context3.next = 31;
              break;
            case 27:
              _context3.prev = 27;
              _context3.t0 = _context3["catch"](3);
              console.error(_context3.t0);
              showEmpty();
            case 31:
              _context3.prev = 31;
              hideLoader();
              return _context3.finish(31);
            case 34:
            case "end":
              return _context3.stop();
          }
        }, _callee3, null, [[3, 27, 31, 34]]);
      }));
      return _getConversations.apply(this, arguments);
    }
    function getMessages(_x, _x2, _x3) {
      return _getMessages.apply(this, arguments);
    }
    function _getMessages() {
      _getMessages = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee4(conversation_id, update, just_send) {
        var _conversations$conver;
        var url, cached, res, response;
        return _regeneratorRuntime().wrap(function _callee4$(_context4) {
          while (1) switch (_context4.prev = _context4.next) {
            case 0:
              url = stm_lms_ajaxurl + "?action=stm_lms_get_user_messages&nonce=" + stm_lms_nonces["stm_lms_get_user_messages"] + "&id=" + conversation_id + "&just_send=" + (just_send ? 1 : 0);
              cached = typeof ((_conversations$conver = conversations[conversationId]) === null || _conversations$conver === void 0 ? void 0 : _conversations$conver.messages) !== "undefined";
              if (!(cached && !update)) {
                _context4.next = 6;
                break;
              }
              renderMessages();
              scrollMessagesBottom();
              return _context4.abrupt("return");
            case 6:
              _context4.prev = 6;
              _context4.next = 9;
              return fetch(url, {
                method: "GET"
              });
            case 9:
              res = _context4.sent;
              if (res.ok) {
                _context4.next = 12;
                break;
              }
              return _context4.abrupt("return");
            case 12:
              _context4.next = 14;
              return res.json();
            case 14:
              response = _context4.sent;
              conversations[conversationId].messages = (response === null || response === void 0 ? void 0 : response.messages) || [];
              renderMessages();
              _context4.next = 22;
              break;
            case 19:
              _context4.prev = 19;
              _context4.t0 = _context4["catch"](6);
              console.error(_context4.t0);
            case 22:
              _context4.prev = 22;
              scrollMessagesBottom();
              return _context4.finish(22);
            case 25:
            case "end":
              return _context4.stop();
          }
        }, _callee4, null, [[6, 19, 22, 25]]);
      }));
      return _getMessages.apply(this, arguments);
    }
    function init() {
      setResponse("");
      updateSendState();
      resizeMessageInput();
      getConversations();
    }
    function showLoader() {
      if ($loader.length) $loader.addClass("masterstudy-account-messages__loader_show");
    }
    function hideLoader() {
      if ($loader.length) $loader.removeClass("masterstudy-account-messages__loader_show");
    }
    function showEmpty() {
      if ($empty.length) $empty.addClass("masterstudy-account-messages__empty_show");
    }
    function hideEmpty() {
      if ($empty.length) $empty.removeClass("masterstudy-account-messages__empty_show");
    }
    function scrollToChatPanel() {
      var $panel = $root.find(".masterstudy-account-messages__panel-card");
      if (!$panel.length) return;
      var top = $panel.offset().top - 40;
      window.scrollTo({
        top: top,
        behavior: "smooth"
      });
    }
    init();
    $root.on("click", ".masterstudy-account-messages__panel-action--refresh", function (e) {
      e.preventDefault();
      if (!conversations.length) return;
      setIsUpdating(true);
      getMessages(conversations[conversationId]["conversation_info"]["conversation_id"], true, false);
    });
    $sendMessage.on("input", function () {
      myMessage = $(this).val();
      updateSendState();
      resizeMessageInput();
    });
    $sendBtn.on("click", /*#__PURE__*/function () {
      var _ref2 = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee2(e) {
        var user_to, msg, data, url, res, response;
        return _regeneratorRuntime().wrap(function _callee2$(_context2) {
          while (1) switch (_context2.prev = _context2.next) {
            case 0:
              e.preventDefault();
              if (conversations.length) {
                _context2.next = 3;
                break;
              }
              return _context2.abrupt("return");
            case 3:
              user_to = conversations[conversationId]["companion"]["id"];
              msg = normalizeMessage(myMessage);
              if (msg) {
                _context2.next = 8;
                break;
              }
              updateSendState();
              return _context2.abrupt("return");
            case 8:
              data = {
                to: user_to,
                message: msg
              };
              url = stm_lms_ajaxurl + "?action=stm_lms_send_message&nonce=" + stm_lms_nonces["stm_lms_send_message"];
              setResponse("");
              _context2.prev = 11;
              setIsLoading(true);
              _context2.next = 15;
              return fetch(url, {
                method: "POST",
                body: JSON.stringify(data)
              });
            case 15:
              res = _context2.sent;
              if (res.ok) {
                _context2.next = 18;
                break;
              }
              return _context2.abrupt("return");
            case 18:
              _context2.next = 20;
              return res.json();
            case 20:
              response = _context2.sent;
              if (!(response.response && response.status === "error")) {
                _context2.next = 24;
                break;
              }
              setResponse(response.response);
              return _context2.abrupt("return");
            case 24:
              _context2.next = 26;
              return getMessages(conversations[conversationId]["conversation_info"]["conversation_id"], true, true);
            case 26:
              myMessage = "";
              $sendMessage.val("");
              updateSendState();
              resizeMessageInput();
              _context2.next = 35;
              break;
            case 32:
              _context2.prev = 32;
              _context2.t0 = _context2["catch"](11);
              console.error(_context2.t0);
            case 35:
              _context2.prev = 35;
              setIsLoading(false);
              return _context2.finish(35);
            case 38:
            case "end":
              return _context2.stop();
          }
        }, _callee2, null, [[11, 32, 35, 38]]);
      }));
      return function (_x4) {
        return _ref2.apply(this, arguments);
      };
    }());
  });
})(jQuery);