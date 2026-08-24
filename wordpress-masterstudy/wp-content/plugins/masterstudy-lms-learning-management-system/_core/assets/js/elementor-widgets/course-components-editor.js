"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
function _iterableToArrayLimit(arr, i) { var _i = null == arr ? null : "undefined" != typeof Symbol && arr[Symbol.iterator] || arr["@@iterator"]; if (null != _i) { var _s, _e, _x, _r, _arr = [], _n = !0, _d = !1; try { if (_x = (_i = _i.call(arr)).next, 0 === i) { if (Object(_i) !== _i) return; _n = !1; } else for (; !(_n = (_s = _x.call(_i)).done) && (_arr.push(_s.value), _arr.length !== i); _n = !0); } catch (err) { _d = !0, _e = err; } finally { try { if (!_n && null != _i["return"] && (_r = _i["return"](), Object(_r) !== _r)) return; } finally { if (_d) throw _e; } } return _arr; } }
function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); Object.defineProperty(subClass, "prototype", { writable: false }); if (superClass) _setPrototypeOf(subClass, superClass); }
function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }
function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }
function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }
var CourseInteractions = /*#__PURE__*/function (_elementorModules$fro) {
  _inherits(CourseInteractions, _elementorModules$fro);
  var _super = _createSuper(CourseInteractions);
  function CourseInteractions() {
    _classCallCheck(this, CourseInteractions);
    return _super.apply(this, arguments);
  }
  _createClass(CourseInteractions, [{
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      return {
        selectors: {
          tabsItem: '.masterstudy-single-course-tabs__item',
          tabsContainer: '.masterstudy-single-course-tabs__container',
          faqItem: '.masterstudy-single-course-faq__item',
          faqAnswer: '.masterstudy-single-course-faq__answer',
          faqContainerWrapper: '.masterstudy-single-course-faq__container-wrapper',
          curriculumToggler: '.masterstudy-curriculum-list__toggler',
          excerptToggler: '.masterstudy-curriculum-list__excerpt-toggler',
          disabledLink: '.masterstudy-curriculum-list__link_disabled',
          hint: '.masterstudy-hint',
          excerptMore: '.masterstudy-single-course-excerpt__more',
          shareButtonTitle: '.masterstudy-single-course-share-button__title',
          shareModal: '.masterstudy-single-course-share-button-modal',
          shareModalClose: '.masterstudy-single-course-share-button-modal__close',
          completeBlock: '.masterstudy-single-course-complete',
          completeDetails: '.masterstudy-single-course-complete-block__details',
          completeButton: '.masterstudy-single-course-complete__buttons .masterstudy-button:last-child',
          completeClose: '.masterstudy-single-course-complete__close'
        }
      };
    }
  }, {
    key: "getDefaultElements",
    value: function getDefaultElements() {
      var _this = this;
      var selectors = this.getSettings('selectors');
      return Object.fromEntries(Object.entries(selectors).map(function (_ref) {
        var _ref2 = _slicedToArray(_ref, 2),
          key = _ref2[0],
          selector = _ref2[1];
        return ["$".concat(key), _this.$element.find(selector)];
      }));
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      var _this2 = this;
      this.elements.$tabsItem.on('click', this.handleTabsClick.bind(this));
      this.elements.$faqItem.on('click', this.toggleFaqItem.bind(this));
      this.elements.$curriculumToggler.on('click', function (event) {
        return _this2.toggleContainer(event, true);
      });
      this.elements.$excerptToggler.on('click', function (event) {
        return _this2.toggleContainer(event, false);
      });
      this.elements.$disabledLink.on('click', function (event) {
        return event.preventDefault();
      });
      this.elements.$hint.hover(this.handleHintHover.bind(this), this.handleHintOut.bind(this));
      this.elements.$excerptMore.on('click', this.toggleExcerpt.bind(this));
      this.elements.$shareButtonTitle.on('click', this.showShareModal.bind(this));
      this.elements.$shareModal.on('click', this.closeShareModal.bind(this));
      this.elements.$shareModalClose.on('click', this.hideShareModal.bind(this));
      this.elements.$completeDetails.on('click', this.showCompleteBlock.bind(this));
      this.elements.$completeBlock.on('click', this.hideCompleteBlock.bind(this));
      this.elements.$completeButton.on('click', this.hideCompleteBlock.bind(this));
      this.elements.$completeClose.on('click', this.hideCompleteBlock.bind(this));
      setTimeout(function () {
        _this2.elements.$shareModal.removeAttr('style');
        _this2.elements.$completeBlock.removeAttr('style');
      }, 1000);
    }
  }, {
    key: "showCompleteBlock",
    value: function showCompleteBlock() {
      jQuery('body').addClass('masterstudy-single-course-complete_hidden');
      this.elements.$completeBlock.addClass('masterstudy-single-course-complete_active');
      this.initCompletionProgress();
    }
  }, {
    key: "hideCompleteBlock",
    value: function hideCompleteBlock() {
      this.elements.$completeBlock.removeClass('masterstudy-single-course-complete_active');
      jQuery('body').removeClass('masterstudy-single-course-complete_hidden');
    }
  }, {
    key: "initCompletionProgress",
    value: function initCompletionProgress() {
      var _this3 = this;
      var course_id = this.elements.$completeBlock.data('course-id');
      jQuery.get("".concat(components_data.ajax_url, "?action=stm_lms_total_progress&course_id=").concat(course_id, "&nonce=").concat(components_data.nonce), function (response) {
        _this3.updateCompletionStats(response);
      });
    }
  }, {
    key: "updateCompletionStats",
    value: function updateCompletionStats(stats) {
      var _this4 = this;
      this.elements.$completeBlock.find('.masterstudy-single-course-complete__loading').hide();
      this.elements.$completeBlock.find('.masterstudy-single-course-complete__success').show();
      this.elements.$completeBlock.find('.masterstudy-single-course-complete__opportunities-percent').text("".concat(stats.course.progress_percent, "%"));
      if (stats.title) {
        this.elements.$completeBlock.find('h2').show().text(stats.title);
      }
      ['lesson', 'multimedia', 'quiz', 'assignment'].forEach(function (type) {
        if (stats.curriculum.hasOwnProperty(type)) {
          _this4.elements.$completeBlock.find(".masterstudy-single-course-complete__curiculum-statistic-item_type-".concat(type)).addClass('show-item');
          _this4.elements.$completeBlock.find(".masterstudy-single-course-complete__curiculum-statistic-item_type-".concat(type, " .masterstudy-single-course-complete__curiculum-statistic-item_completed")).html(stats.curriculum[type].completed);
          _this4.elements.$completeBlock.find(".masterstudy-single-course-complete__curiculum-statistic-item_type-".concat(type, " .masterstudy-single-course-complete__curiculum-statistic-item_total")).html(stats.curriculum[type].total);
        }
      });
    }
  }, {
    key: "handleTabsClick",
    value: function handleTabsClick(event) {
      var $tab = jQuery(event.currentTarget);
      var targetId = $tab.data('id');
      $tab.siblings().removeClass('masterstudy-single-course-tabs__item_active');
      $tab.addClass('masterstudy-single-course-tabs__item_active');
      this.elements.$tabsContainer.removeClass('masterstudy-single-course-tabs__container_active').filter(function (_, el) {
        return jQuery(el).data('id') === targetId;
      }).addClass('masterstudy-single-course-tabs__container_active');
    }
  }, {
    key: "toggleFaqItem",
    value: function toggleFaqItem(event) {
      var $faqItem = jQuery(event.currentTarget);
      var content = $faqItem.find(this.getSettings('selectors').faqAnswer);
      var isOpened = content.is(':visible');
      var openedClass = 'masterstudy-single-course-faq__container-wrapper_opened';
      if (isOpened) {
        content.animate({
          height: 0
        }, 100, function () {
          setTimeout(function () {
            content.css('display', 'none').css('height', '');
          }, 300);
        });
        $faqItem.find(this.getSettings('selectors').faqContainerWrapper).removeClass(openedClass);
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
        $faqItem.find(this.getSettings('selectors').faqContainerWrapper).addClass(openedClass);
      }
    }
  }, {
    key: "toggleContainer",
    value: function toggleContainer(event, main) {
      event.preventDefault();
      var content = main ? jQuery(event.currentTarget).parent().next() : jQuery(event.currentTarget).parent().parent().next();
      var isOpened = content.is(':visible');
      var openedClass = main ? 'masterstudy-curriculum-list__wrapper_opened' : 'masterstudy-curriculum-list__container-wrapper_opened';
      if (isOpened) {
        content.animate({
          height: 0
        }, 100, function () {
          setTimeout(function () {
            content.css('display', 'none').css('height', '');
          }, 300);
        });
        jQuery(event.currentTarget).parent().parent().removeClass(openedClass);
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
        jQuery(event.currentTarget).parent().parent().addClass(openedClass);
      }
    }
  }, {
    key: "handleHintHover",
    value: function handleHintHover(event) {
      jQuery(event.currentTarget).closest('.masterstudy-curriculum-list__materials').css('overflow', 'visible');
    }
  }, {
    key: "handleHintOut",
    value: function handleHintOut(event) {
      jQuery(event.currentTarget).closest('.masterstudy-curriculum-list__materials').css('overflow', 'hidden');
    }
  }, {
    key: "toggleExcerpt",
    value: function toggleExcerpt(event) {
      var $button = jQuery(event.currentTarget);
      $button.siblings('.masterstudy-single-course-excerpt__hidden').toggle();
      $button.siblings('.masterstudy-single-course-excerpt__continue').toggle();
      $button.text($button.text().trim() === components_data.more_title ? components_data.less_title : components_data.more_title);
    }
  }, {
    key: "showShareModal",
    value: function showShareModal() {
      this.elements.$shareModal.addClass('masterstudy-single-course-share-button-modal_open');
      jQuery('body').addClass('masterstudy-single-course-share-button-body-hidden');
    }
  }, {
    key: "closeShareModal",
    value: function closeShareModal(event) {
      if (event.target === event.currentTarget) {
        this.hideShareModal();
      }
    }
  }, {
    key: "hideShareModal",
    value: function hideShareModal() {
      this.elements.$shareModal.removeClass('masterstudy-single-course-share-button-modal_open');
      jQuery('body').removeClass('masterstudy-single-course-share-button-body-hidden');
    }
  }]);
  return CourseInteractions;
}(elementorModules.frontend.handlers.Base);
jQuery(window).on('elementor/frontend/init', function () {
  var addHandler = function addHandler($element) {
    elementorFrontend.elementsHandler.addHandler(CourseInteractions, {
      $element: $element
    });
  };
  elementorFrontend.hooks.addAction('frontend/element_ready/ms_lms_course_faq.default', addHandler);
  elementorFrontend.hooks.addAction('frontend/element_ready/ms_lms_course_curriculum.default', addHandler);
  elementorFrontend.hooks.addAction('frontend/element_ready/ms_lms_course_excerpt.default', addHandler);
  elementorFrontend.hooks.addAction('frontend/element_ready/ms_lms_course_share_button.default', addHandler);
  elementorFrontend.hooks.addAction('frontend/element_ready/ms_lms_course_complete.default', addHandler);
});