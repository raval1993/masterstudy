"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, defineProperty = Object.defineProperty || function (obj, key, desc) { obj[key] = desc.value; }, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return defineProperty(generator, "_invoke", { value: makeInvokeMethod(innerFn, self, context) }), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; defineProperty(this, "_invoke", { value: function value(method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; } function maybeInvokeDelegate(delegate, context) { var methodName = context.method, method = delegate.iterator[methodName]; if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel; var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), defineProperty(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (val) { var object = Object(val), keys = []; for (var key in object) keys.push(key); return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
(function ($) {
  $(document).ready(function () {
    var _video_player_data$vi, _video_player_data$vi2, _video_player_data$vi3, _video_player_data$qu, _video_player_data$vi4, _video_player_data$vi5;
    var videoPlayerWrapper = $('.masterstudy-course-player-lesson-video__wrapper');
    var videoPlayerContainer = $('.masterstudy-course-player-lesson-video');
    var videoWrapper = $('.masterstudy-course-player-lesson-video__container');
    var playButton = $('.masterstudy-course-player-lesson-video__play-button');
    var currentProgressContainer = $('#current-video-progress');
    var currentUserProgressContainer = $('#current-video-progress-user');
    var iframe = document.getElementById('videoPlayer');
    var requiredProgress = parseInt($('#required-video-progress').data('required-progress'), 10) || 0;
    var videoMarkers = (_video_player_data$vi = video_player_data.video_markers) !== null && _video_player_data$vi !== void 0 ? _video_player_data$vi : [];
    var videoQuestions = (_video_player_data$vi2 = video_player_data.video_questions) !== null && _video_player_data$vi2 !== void 0 ? _video_player_data$vi2 : [];
    var totalQuestions = (_video_player_data$vi3 = video_player_data.video_questions_stats['total']) !== null && _video_player_data$vi3 !== void 0 ? _video_player_data$vi3 : 0;
    var questionsMustDone = (_video_player_data$qu = video_player_data.questions_must_done) !== null && _video_player_data$qu !== void 0 ? _video_player_data$qu : false;
    var questionsProgressContainer = $('#current-questions-progress-user');
    var questionsProgressBar = $('#current-questions-progress');
    var completedQuestions = (_video_player_data$vi4 = video_player_data.video_questions_stats['completed']) !== null && _video_player_data$vi4 !== void 0 ? _video_player_data$vi4 : 0;
    var answeredQuestions = (_video_player_data$vi5 = video_player_data.video_questions_stats['answered']) !== null && _video_player_data$vi5 !== void 0 ? _video_player_data$vi5 : 0;
    var userProgress = parseInt(currentProgressContainer.data('progress'), 10) || 0;
    var submitButton = $('[data-id="masterstudy-course-player-lesson-submit"]');
    var hint = $('.masterstudy-course-player-navigation__next .masterstudy-hint');
    var dataQuery = submitButton.attr('data-query');
    var lastCheckedSecond = -1;
    var wasFullscreen = false;
    var initialLoad = true;
    var youTubePlayer = null;
    var vimeoPlayer = null;
    var plyrVideoPlayer = null;
    var prestoPlayer = null;
    var playerVDO = null;
    var videoDefault = null;
    var videoElement = null;
    var lastTime = 0;
    var exposedCurrentTime = 0;
    var isSeeking = false;
    if (userProgress < requiredProgress || questionsMustDone && completedQuestions < totalQuestions) {
      submitButton.attr('disabled', 1);
      submitButton.addClass('masterstudy-button_disabled');
    }
    videoElement = $('.masterstudy-course-player-lesson-video__wrapper video');
    if (videoElement.length) {
      blockSeeking('html5', videoElement.get(0));
      videoElement.click(function () {
        $(this).siblings('span').hide();
      });
      $('body').on('click', '.masterstudy-timecode', function () {
        if (video_player_data.strict_mode) return;
        if ($('.masterstudy-lesson-video-question').hasClass('masterstudy-lesson-video-question_show')) {
          return;
        }
        var timecode = parseInt($(this).data('timecode'), 10);
        if (!isNaN(timecode)) {
          videoDefault = videoElement.get(0);
          if (videoDefault) {
            videoPlayerContainer[0].scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
            videoDefault.currentTime = timecode;
            if (videoDefault.paused) {
              videoDefault.play();
            }
          }
        }
      });
      videoElement.on('timeupdate', function () {
        if (!isSeeking) {
          checkVideoQuestions(this.currentTime);
        }
      });
    }
    if (videoPlayerWrapper.length && playButton.length) {
      playButton.click(function () {
        playButton.hide();
        videoPlayerWrapper.find('video').get(0).play();
      });
      videoPlayerWrapper.on('play', function () {
        playButton.hide();
      });
      videoPlayerWrapper.on('pause', function () {
        if (!window.matchMedia('(max-width: 576px)').matches) {
          playButton.show();
        }
      });
    }
    if (iframe) {
      if ('youtube' === video_player_data.video_type && !video_player_data.plyr_youtube_player) {
        loadYouTubeAPI().then(function () {
          return initYouTubePlayer();
        });
        $('body').on('click', '.masterstudy-timecode', /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee3() {
          var timecode;
          return _regeneratorRuntime().wrap(function _callee3$(_context3) {
            while (1) switch (_context3.prev = _context3.next) {
              case 0:
                if (!video_player_data.strict_mode) {
                  _context3.next = 2;
                  break;
                }
                return _context3.abrupt("return");
              case 2:
                if (!$('.masterstudy-lesson-video-question').hasClass('masterstudy-lesson-video-question_show')) {
                  _context3.next = 4;
                  break;
                }
                return _context3.abrupt("return");
              case 4:
                timecode = parseInt($(this).data('timecode'), 10);
                if (!isNaN(timecode)) {
                  _context3.next = 7;
                  break;
                }
                return _context3.abrupt("return");
              case 7:
                videoPlayerContainer[0].scrollIntoView({
                  behavior: 'smooth',
                  block: 'start'
                });
                _context3.next = 10;
                return waitForYouTubePlayer();
              case 10:
                if (youTubePlayer && typeof youTubePlayer.seekTo === 'function') {
                  youTubePlayer.seekTo(timecode, true);
                  setTimeout( /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee2() {
                    var state;
                    return _regeneratorRuntime().wrap(function _callee2$(_context2) {
                      while (1) switch (_context2.prev = _context2.next) {
                        case 0:
                          state = youTubePlayer.getPlayerState();
                          if (!(state !== YT.PlayerState.PLAYING)) {
                            _context2.next = 4;
                            break;
                          }
                          _context2.next = 4;
                          return youTubePlayer.playVideo();
                        case 4:
                          setTimeout( /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
                            return _regeneratorRuntime().wrap(function _callee$(_context) {
                              while (1) switch (_context.prev = _context.next) {
                                case 0:
                                  state = youTubePlayer.getPlayerState();
                                  if (!(state !== YT.PlayerState.PLAYING)) {
                                    _context.next = 4;
                                    break;
                                  }
                                  _context.next = 4;
                                  return youTubePlayer.playVideo();
                                case 4:
                                case "end":
                                  return _context.stop();
                              }
                            }, _callee);
                          })), 500);
                        case 5:
                        case "end":
                          return _context2.stop();
                      }
                    }, _callee2);
                  })), 300);
                }
              case 11:
              case "end":
                return _context3.stop();
            }
          }, _callee3, this);
        })));
      } else if ('vimeo' === video_player_data.video_type && !video_player_data.plyr_vimeo_player) {
        vimeoPlayer = new Vimeo.Player(iframe);
        blockSeeking('vimeo', vimeoPlayer);
        vimeoPlayer.on('timeupdate', function (data) {
          if (!isSeeking) {
            onTimeUpdate(data.seconds, data.duration);
            checkVideoQuestions(data.seconds);
          }
        });
        vimeoPlayer.on('ended', finalizeProgress);
        $('body').on('click', '.masterstudy-timecode', function () {
          if (video_player_data.strict_mode) return;
          if ($('.masterstudy-lesson-video-question').hasClass('masterstudy-lesson-video-question_show')) {
            return;
          }
          var timecode = parseInt($(this).data('timecode'), 10);
          if (!isNaN(timecode)) {
            videoPlayerContainer[0].scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
            vimeoPlayer.setCurrentTime(timecode).then(function () {
              vimeoPlayer.play();
            });
          }
        });
      }
    }
    function waitForYouTubePlayer() {
      return _waitForYouTubePlayer.apply(this, arguments);
    }
    function _waitForYouTubePlayer() {
      _waitForYouTubePlayer = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee4() {
        return _regeneratorRuntime().wrap(function _callee4$(_context4) {
          while (1) switch (_context4.prev = _context4.next) {
            case 0:
              if (!(youTubePlayer && typeof youTubePlayer.seekTo === 'function')) {
                _context4.next = 3;
                break;
              }
              blockSeeking('youtube', youTubePlayer);
              return _context4.abrupt("return");
            case 3:
              _context4.next = 5;
              return loadYouTubeAPI();
            case 5:
              _context4.next = 7;
              return initYouTubePlayer();
            case 7:
            case "end":
              return _context4.stop();
          }
        }, _callee4);
      }));
      return _waitForYouTubePlayer.apply(this, arguments);
    }
    function loadYouTubeAPI() {
      return new Promise(function (resolve) {
        if (window.YT && typeof window.YT.Player === 'function') {
          resolve();
          return;
        }
        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        window.onYouTubeIframeAPIReady = function () {
          resolve();
        };
      });
    }
    function initYouTubePlayer() {
      return new Promise(function (resolve) {
        if (!window.YT || !window.YT.Player) {
          resolve();
          return;
        }
        youTubePlayer = new YT.Player('videoPlayer', {
          events: {
            'onStateChange': onPlayerStateChange
          }
        });
        var checkPlayerReady = setInterval(function () {
          if (youTubePlayer && typeof youTubePlayer.seekTo === 'function') {
            clearInterval(checkPlayerReady);
            resolve();
          }
        }, 500);
      });
    }
    var plyrVideoPlayerContainer = document.querySelector('.masterstudy-course-player-lesson-video .masterstudy-plyr-video-player');
    if (plyrVideoPlayerContainer) {
      plyrVideoPlayer = new Plyr($(plyrVideoPlayerContainer), {
        invertTime: true,
        markers: {
          enabled: true,
          points: videoMarkers
        }
      });
      var overlay = $('<div>').addClass('plyr-overlay');
      var _iframe = $(plyrVideoPlayer).find('iframe');
      if (_iframe.length) {
        _iframe.before(overlay);
        _iframe.after(overlay.clone());
      }
      blockSeeking('plyr', plyrVideoPlayer);
      plyrVideoPlayer.on('timeupdate', function (event) {
        var currentTime = event.detail.plyr.currentTime || 0;
        var duration = event.detail.plyr.duration || 0;
        if (!isSeeking) {
          onTimeUpdate(currentTime, duration);
          checkVideoQuestions(currentTime);
        }
      });
      plyrVideoPlayer.on('ended', finalizeProgress);
      $('body').on('click', '.masterstudy-timecode', function () {
        if (video_player_data.strict_mode) return;
        if ($('.masterstudy-lesson-video-question').hasClass('masterstudy-lesson-video-question_show')) {
          return;
        }
        var timecode = parseInt($(this).data('timecode'), 10);
        if (!isNaN(timecode)) {
          videoPlayerContainer[0].scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
          if (video_player_data.video_type === 'youtube') {
            if (plyrVideoPlayer.embed && plyrVideoPlayer.embed.playVideo) {
              plyrVideoPlayer.embed.seekTo(timecode, true);
              if (plyrVideoPlayer.embed.getPlayerState() !== YT.PlayerState.PLAYING) {
                plyrVideoPlayer.embed.playVideo();
                plyrVideoPlayer.play();
              }
            }
          } else if (video_player_data.video_type === 'vimeo') {
            if (plyrVideoPlayer.embed && plyrVideoPlayer.embed.setCurrentTime) {
              plyrVideoPlayer.embed.setCurrentTime(timecode).then(function () {
                plyrVideoPlayer.embed.getPaused().then(function (paused) {
                  if (paused) {
                    plyrVideoPlayer.embed.play();
                    plyrVideoPlayer.play();
                  }
                });
              });
            }
          } else {
            plyrVideoPlayer.currentTime = timecode;
            if (plyrVideoPlayer.paused) {
              plyrVideoPlayer.play();
            }
          }
        }
      });
    }
    prestoPlayer = document.querySelector('.masterstudy-course-player-lesson-video presto-player');
    if (prestoPlayer) {
      blockSeeking('presto', prestoPlayer);
      wp.hooks.addAction('presto.playerTimeUpdate', 'masterstudy-presto-time-update', function (player) {
        var currentTime = player.currentTime || 0;
        var duration = player.duration || 0;
        if (!isSeeking) {
          onTimeUpdate(currentTime, duration);
          checkVideoQuestions(currentTime);
        }
      });
      wp.hooks.addAction('presto.playerEnded', 'masterstudy-presto-ended', function (player) {
        finalizeProgress();
      });
      $('body').on('click', '.masterstudy-timecode', function () {
        if (video_player_data.strict_mode) return;
        if ($('.masterstudy-lesson-video-question').hasClass('masterstudy-lesson-video-question_show')) {
          return;
        }
        var timecode = parseInt($(this).data('timecode'), 10);
        if (!isNaN(timecode)) {
          videoPlayerContainer[0].scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
          prestoPlayer.currentTime = timecode;
          if (!prestoPlayer.playerPlaying) {
            prestoPlayer.play();
          }
        }
      });
    }
    var vdoIframe = document.querySelector('.masterstudy-course-player-lesson-video iframe[src*="vdocipher.com"]');
    if (vdoIframe) {
      playerVDO = VdoPlayer.getInstance(vdoIframe);
      if (playerVDO && playerVDO.video) {
        blockSeeking('vdocipher', playerVDO);
        playerVDO.video.addEventListener('timeupdate', function () {
          var currentTime = playerVDO.video.currentTime || 0;
          var duration = playerVDO.video.duration || 0;
          if (!isSeeking) {
            onTimeUpdate(currentTime, duration);
            checkVideoQuestions(currentTime);
          }
        });
        playerVDO.video.addEventListener('ended', finalizeProgress);
        $('body').on('click', '.masterstudy-timecode', function () {
          if (video_player_data.strict_mode) return;
          if ($('.masterstudy-lesson-video-question').hasClass('masterstudy-lesson-video-question_show')) {
            return;
          }
          var timecode = parseInt($(this).data('timecode'), 10);
          if (!isNaN(timecode)) {
            videoPlayerContainer[0].scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
            playerVDO.video.currentTime = timecode;
          }
        });
      }
    }
    $('body').on('click', '[data-id="masterstudy-video-question-skip"], [data-id="masterstudy-video-question-continue"]', function () {
      $('.masterstudy-lesson-video-question').removeClass('masterstudy-lesson-video-question_show');
      videoWrapper.show();
      toggleVideoPlayback();
      if (wasFullscreen) {
        enterFullscreenIfNeeded();
      }
    });
    $('body').on('click', '.masterstudy-lesson-video-list-question', function () {
      var timecode = parseInt($(this).data('marker'), 10);
      if (isNaN(timecode)) return;
      if ($(this).hasClass('masterstudy-lesson-video-list-question_completed')) return;
      var isFailed = $(this).hasClass('masterstudy-lesson-video-list-question_failed');
      if (!isFailed && video_player_data.strict_mode) {
        return;
      }
      var relatedQuestionBlock = $('.masterstudy-lesson-video-question[data-marker="' + timecode + '"]');
      if (relatedQuestionBlock.hasClass('masterstudy-lesson-video-question_show')) {
        return;
      }
      lastCheckedSecond = -1;
      if (isFailed && video_player_data.strict_mode) {
        isSeeking = false;
        lastTime = timecode;
        checkVideoQuestions(timecode);
      }
      videoPlayerContainer[0].scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
      playerSeekTo(timecode);
      if (isFailed && video_player_data.strict_mode) {
        toggleVideoPlayback(true);
      }
    });
    $('body').on('click', '[data-id="masterstudy-video-question-rewatch"]', function (event) {
      event.preventDefault();
      var questionElement = $(this).closest('.masterstudy-lesson-video-question');
      if (questionElement.hasClass('masterstudy-lesson-video-question_answered')) {
        var currentProgress = parseInt(questionsProgressContainer.text(), 10) || 0;
        if (currentProgress > 0) {
          questionsProgressContainer.text(currentProgress - 1);
          answeredQuestions--;
          var progressPercent = answeredQuestions / totalQuestions * 100;
          questionsProgressBar.css('width', progressPercent + '%');
        }
      }
      questionElement.removeClass('masterstudy-lesson-video-question_show masterstudy-lesson-video-question_answered masterstudy-lesson-video-question_failed');
      videoWrapper.show();
      questionElement.find('.masterstudy-lesson-video-question__answer').removeClass('masterstudy-lesson-video-question__answer_selected masterstudy-lesson-video-question__answer_failed masterstudy-lesson-video-question__answer_completed');
      questionElement.find('.masterstudy-lesson-video-question__radio_checked, .masterstudy-lesson-video-question__checkbox_checked').removeClass('masterstudy-lesson-video-question__radio_checked masterstudy-lesson-video-question__checkbox_checked');
      questionElement.find('input[type="radio"], input[type="checkbox"]').prop('checked', false);
      var timecode = parseInt($(this).parent().data('rewatch'), 10);
      if (video_player_data.strict_mode) {
        isSeeking = false;
        lastTime = timecode;
      }
      if (!isNaN(timecode)) {
        playerSeekTo(timecode);
      }
    });
    $('body').on('click', '[data-id="masterstudy-video-question-submit"]', function (e) {
      e.preventDefault();
      var _this = $(this);
      var current_question = $(this).closest('.masterstudy-lesson-video-question');
      var question_id = current_question.attr('id');
      var list_question = $('.masterstudy-lesson-video-list-question').filter(function () {
        return $(this).attr('id') === question_id;
      });
      var selected_answers = current_question.find('input:checked').map(function () {
        return $(this).attr('id');
      }).get();
      var current_answer = selected_answers.join(',');
      $.ajax({
        url: stm_lms_ajaxurl,
        type: 'POST',
        data: {
          action: 'stm_lms_answer_video_lesson',
          nonce: video_player_data.video_questions_nonce,
          course_id: parseInt(video_player_data.course_id),
          lesson_id: parseInt(video_player_data.lesson_id),
          question_id: parseInt(question_id),
          user_answer: current_answer
        },
        beforeSend: function beforeSend() {
          _this.addClass('masterstudy-button_loading');
        },
        success: function success(response) {
          if (response.result === 'correct') {
            var currentProgress = parseInt(questionsProgressContainer.text()) || 0;
            questionsProgressContainer.text(currentProgress + 1);
            current_question.addClass('masterstudy-lesson-video-question_answered masterstudy-lesson-video-question_completed');
            list_question.removeClass('masterstudy-lesson-video-list-question_failed').addClass('masterstudy-lesson-video-list-question_completed');
            selected_answers.forEach(function (answer_id) {
              var answerElement = current_question.find('#' + answer_id).closest('.masterstudy-lesson-video-question__answer');
              answerElement.addClass('masterstudy-lesson-video-question__answer_selected masterstudy-lesson-video-question__answer_completed');
            });
            completedQuestions++;
            updateQuestionsProgress();
            console.log(userProgress);
            if (questionsMustDone && completedQuestions === totalQuestions && userProgress >= requiredProgress) {
              hint.hide();
              submitButton.removeAttr('disabled');
              submitButton.removeClass('masterstudy-button_disabled');
            }
          } else if (response.result === 'wrong') {
            var _currentProgress = parseInt(questionsProgressContainer.text()) || 0;
            questionsProgressContainer.text(_currentProgress + 1);
            current_question.addClass('masterstudy-lesson-video-question_answered masterstudy-lesson-video-question_failed');
            list_question.removeClass('masterstudy-lesson-video-list-question_completed').addClass('masterstudy-lesson-video-list-question_failed');
            selected_answers.forEach(function (answer_id) {
              var answerElement = current_question.find('#' + answer_id).closest('.masterstudy-lesson-video-question__answer');
              var answerData = response.user_answer.find(function (a) {
                return a.id == answer_id;
              });
              if (answerData) {
                if (answerData.status === 'correct') {
                  answerElement.addClass('masterstudy-lesson-video-question__answer_selected masterstudy-lesson-video-question__answer_completed');
                } else {
                  answerElement.addClass('masterstudy-lesson-video-question__answer_selected masterstudy-lesson-video-question__answer_failed');
                }
              }
            });
            updateQuestionsProgress();
          }
        },
        complete: function complete() {
          _this.removeClass('masterstudy-button_loading');
        }
      });
    });
    window.MasterstudyCoursePlayerVideo = {
      getCurrentTime: function getCurrentTime() {
        if (videoElement.length && videoElement.get(0)) {
          return Math.floor(videoElement.get(0).currentTime || 0);
        }
        if (youTubePlayer && typeof youTubePlayer.getCurrentTime === 'function') {
          return Math.floor(youTubePlayer.getCurrentTime() || 0);
        }
        if (plyrVideoPlayer) {
          return Math.floor(plyrVideoPlayer.currentTime || 0);
        }
        if (prestoPlayer) {
          return Math.floor(prestoPlayer.currentTime || 0);
        }
        if (playerVDO && playerVDO.video) {
          return Math.floor(playerVDO.video.currentTime || 0);
        }
        return Math.floor(exposedCurrentTime || lastTime || 0);
      },
      seekTo: function seekTo(timecode, options) {
        options = options || {};
        if (options.force && video_player_data.strict_mode) {
          isSeeking = false;
          lastTime = timecode;
          exposedCurrentTime = timecode;
        }
        playerSeekTo(timecode);
      }
    };
    function playerSeekTo(timecode) {
      if (videoElement.length) {
        videoDefault = videoElement.get(0);
        if (videoDefault) {
          videoDefault.currentTime = timecode;
          if (videoDefault.paused) {
            videoDefault.play();
          }
        }
      }
      if (youTubePlayer && typeof youTubePlayer.seekTo === 'function') {
        youTubePlayer.seekTo(timecode, true);
        if (youTubePlayer.getPlayerState() !== YT.PlayerState.PLAYING) {
          youTubePlayer.playVideo();
        }
      }
      if (vimeoPlayer) {
        vimeoPlayer.setCurrentTime(timecode).then(function () {
          vimeoPlayer.play();
        });
      }
      if (plyrVideoPlayer) {
        plyrVideoPlayer.currentTime = timecode;
        if (plyrVideoPlayer.paused) {
          plyrVideoPlayer.play();
        }
      }
      if (prestoPlayer) {
        prestoPlayer.currentTime = timecode;
        if (!prestoPlayer.playerPlaying) {
          prestoPlayer.play();
        }
      }
      if (playerVDO && playerVDO.video) {
        playerVDO.video.currentTime = timecode;
        if (playerVDO.video.paused) {
          playerVDO.video.play();
        }
      }
    }
    function onPlayerStateChange(event) {
      if (event.data == YT.PlayerState.PLAYING || event.data === YT.PlayerState.PAUSED) {
        updateYouTubeProgress();
      } else if (event.data === YT.PlayerState.ENDED) {
        finalizeProgress();
      }
    }
    function updateYouTubeProgress() {
      if (youTubePlayer) {
        var currentTime = Math.floor(youTubePlayer.getCurrentTime()) || 0;
        var duration = Math.floor(youTubePlayer.getDuration()) || 0;
        var playerState = youTubePlayer.getPlayerState();
        if (video_player_data.strict_mode) {
          if (Math.abs(currentTime - lastTime) > 1) {
            isSeeking = true;
            youTubePlayer.seekTo(lastTime, true);
            if (youTubePlayer.getPlayerState() === YT.PlayerState.PAUSED) {
              youTubePlayer.playVideo();
            }
            isSeeking = false;
            return;
          }
          lastTime = currentTime;
        }
        if (!isSeeking) {
          checkVideoQuestions(currentTime);
          onTimeUpdate(currentTime, duration);
          if (playerState === YT.PlayerState.PLAYING || playerState === YT.PlayerState.PAUSED) {
            requestAnimationFrame(updateYouTubeProgress);
          }
        }
      }
    }
    function onTimeUpdate(currentTime, duration) {
      exposedCurrentTime = Math.floor(currentTime || 0);
      if (initialLoad && userProgress > 0) {
        return;
      }
      initialLoad = false;
      if (duration > 0 && video_player_data.video_progress) {
        var progress = Math.floor(currentTime / duration * 100);
        if (userProgress >= requiredProgress) {
          if (totalQuestions > 0 && questionsMustDone) {
            if (completedQuestions === totalQuestions) {
              hint.hide();
              submitButton.removeAttr('disabled');
              submitButton.removeClass('masterstudy-button_disabled');
            }
          } else {
            hint.hide();
            submitButton.removeAttr('disabled');
            submitButton.removeClass('masterstudy-button_disabled');
          }
        }
        if (userProgress > progress) {
          return;
        }
        if (progress > 100) userProgress = 100;
        userProgress = progress;
        if (dataQuery) {
          var queryObject = JSON.parse(dataQuery);
          queryObject.progress = userProgress;
          submitButton.attr('data-query', JSON.stringify(queryObject));
        }
        if (currentProgressContainer) {
          currentUserProgressContainer.text("".concat(userProgress, "%"));
          currentProgressContainer.css('width', "".concat(userProgress, "%"));
          currentProgressContainer.attr('data-progress', userProgress);
        }
      }
    }
    function finalizeProgress() {
      if (currentProgressContainer && video_player_data.video_progress) {
        userProgress = 100;
        if (dataQuery) {
          var queryObject = JSON.parse(dataQuery);
          queryObject.progress = userProgress;
          submitButton.attr('data-query', JSON.stringify(queryObject));
          if (totalQuestions > 0 && questionsMustDone) {
            if (completedQuestions === totalQuestions) {
              hint.hide();
              submitButton.removeAttr('disabled');
              submitButton.removeClass('masterstudy-button_disabled');
            }
          } else {
            hint.hide();
            submitButton.removeAttr('disabled');
            submitButton.removeClass('masterstudy-button_disabled');
          }
        }
        currentUserProgressContainer.text("".concat(userProgress, "%"));
        currentProgressContainer.css('width', "".concat(userProgress, "%"));
        currentProgressContainer.attr('data-progress', userProgress);
      }
    }
    function checkVideoQuestions(currentTime) {
      var currentSecond = Math.floor(currentTime);
      if (currentSecond !== lastCheckedSecond) {
        lastCheckedSecond = currentSecond;
        videoQuestions.forEach(function (question) {
          var questionElement = $("#".concat(question.id));
          var markerTime = question.marker;
          if (currentSecond === parseInt(markerTime) && !questionElement.hasClass('masterstudy-lesson-video-question_completed') && !questionElement.hasClass('masterstudy-lesson-video-question_show')) {
            exitFullscreenIfNeeded();
            toggleVideoPlayback(true);
            $('.masterstudy-lesson-video-question').removeClass('masterstudy-lesson-video-question_show');
            questionElement.addClass('masterstudy-lesson-video-question_show');
            videoWrapper.hide();
          }
        });
      }
    }
    function updateQuestionsProgress() {
      if (totalQuestions > 0) {
        answeredQuestions++;
        var progressPercent = answeredQuestions / totalQuestions * 100;
        questionsProgressBar.css('width', progressPercent + '%');
      }
    }
    function toggleVideoPlayback(shouldPause) {
      if (videoElement.length) {
        var video = videoElement.get(0);
        if (video) {
          shouldPause ? video.pause() : video.play();
        }
      }
      if (youTubePlayer) {
        shouldPause ? youTubePlayer.pauseVideo() : youTubePlayer.playVideo();
      }
      if (vimeoPlayer) {
        shouldPause ? vimeoPlayer.pause() : vimeoPlayer.play();
      }
      if (prestoPlayer) {
        shouldPause ? prestoPlayer.pause() : prestoPlayer.play();
      }
      if (plyrVideoPlayer) {
        shouldPause ? plyrVideoPlayer.pause() : plyrVideoPlayer.play();
      }
      if (playerVDO && playerVDO.video) {
        shouldPause ? playerVDO.video.pause() : playerVDO.video.play();
      }
    }
    function isFullscreen() {
      return document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement;
    }
    function exitFullscreenIfNeeded() {
      if (isFullscreen()) {
        wasFullscreen = true;
        if (document.exitFullscreen) {
          document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
          document.webkitExitFullscreen();
        } else if (document.mozCancelFullScreen) {
          document.mozCancelFullScreen();
        } else if (document.msExitFullscreen) {
          document.msExitFullscreen();
        }
      }
    }
    function enterFullscreenIfNeeded() {
      if (plyrVideoPlayer) {
        plyrVideoPlayer.fullscreen.enter();
      }
      wasFullscreen = false;
    }
    function blockSeeking(playerType, playerInstance) {
      if (!playerInstance || !video_player_data.strict_mode) return;
      switch (playerType) {
        case 'html5':
          playerInstance.addEventListener('timeupdate', function () {
            var currentTime = playerInstance.currentTime;
            if (Math.abs(currentTime - lastTime) > 1) {
              playerInstance.currentTime = lastTime;
              isSeeking = true;
              return;
            }
            lastTime = currentTime;
            isSeeking = false;
          });
          break;
        case 'vimeo':
          playerInstance.on('timeupdate', function (data) {
            var currentTime = data.seconds;
            if (Math.abs(currentTime - lastTime) > 1) {
              playerInstance.setCurrentTime(lastTime);
              isSeeking = true;
              return;
            }
            lastTime = currentTime;
            isSeeking = false;
          });
          break;
        case 'plyr':
          playerInstance.on('timeupdate', function (data) {
            var currentTime = playerInstance.currentTime;
            if (Math.abs(currentTime - lastTime) > 1) {
              playerInstance.currentTime = lastTime;
              isSeeking = true;
              return;
            }
            lastTime = currentTime;
            isSeeking = false;
          });
          break;
        case 'presto':
          wp.hooks.addAction('presto.playerTimeUpdate', 'track-presto-time', function (player) {
            var currentTime = player.currentTime;
            if (Math.abs(currentTime - lastTime) > 1) {
              player.currentTime = lastTime;
              isSeeking = true;
              return;
            }
            lastTime = currentTime;
            isSeeking = false;
          });
          break;
        case 'vdocipher':
          playerInstance.video.addEventListener('timeupdate', function () {
            var currentTime = playerInstance.video.currentTime;
            if (Math.abs(currentTime - lastTime) > 1) {
              playerInstance.video.currentTime = lastTime;
              isSeeking = true;
              return;
            }
            lastTime = currentTime;
            isSeeking = false;
          });
          break;
      }
    }
  });
})(jQuery);