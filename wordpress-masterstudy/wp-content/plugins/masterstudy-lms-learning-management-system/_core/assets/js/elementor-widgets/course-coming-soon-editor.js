"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
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
var CourseComingSoon = /*#__PURE__*/function (_elementorModules$fro) {
  _inherits(CourseComingSoon, _elementorModules$fro);
  var _super = _createSuper(CourseComingSoon);
  function CourseComingSoon() {
    _classCallCheck(this, CourseComingSoon);
    return _super.apply(this, arguments);
  }
  _createClass(CourseComingSoon, [{
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      return {
        selectors: {
          notifyAlertButton: '.coming-soon-notify-alert',
          notifyContainer: '.coming-soon-notify-container',
          notifyModalWrapper: '.masterstudy-coming-soon-modal',
          notifyMeBtn: '.coming-soon-notify-container .masterstudy-button',
          notifyInput: '.coming-soon-notify-container input',
          modalClose: '.masterstudy-coming-soon-modal__close',
          modalButton: '.masterstudy-coming-soon-modal .masterstudy-button',
          curriculumPreview: '.stm-curriculum-item .stm-curriculum-item__preview',
          countdown: '.masterstudy-countdown'
        }
      };
    }
  }, {
    key: "getDefaultElements",
    value: function getDefaultElements() {
      var selectors = this.getSettings('selectors');
      return {
        $notifyAlertButton: this.$element.find(selectors.notifyAlertButton),
        $notifyContainer: this.$element.find(selectors.notifyContainer),
        $notifyModalWrapper: this.$element.find(selectors.notifyModalWrapper),
        $notifyMeBtn: this.$element.find(selectors.notifyMeBtn),
        $notifyInput: this.$element.find(selectors.notifyInput),
        $modalClose: this.$element.find(selectors.modalClose),
        $modalButton: this.$element.find(selectors.modalButton),
        $curriculumPreview: this.$element.find(selectors.curriculumPreview),
        $countdown: this.$element.find(selectors.countdown)
      };
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      var _this = this;
      this.elements.$notifyAlertButton.on('click', function () {
        return _this.handleNotifyAlertClick();
      });
      this.elements.$notifyInput.on('input', function (event) {
        return _this.handleInputChange(event);
      });
      this.elements.$notifyInput.on('keypress', function (event) {
        return _this.handleKeyPress(event);
      });
      this.elements.$notifyMeBtn.on('click', function (event) {
        return _this.handleNotifyMeClick(event);
      });
      this.elements.$modalClose.add(this.elements.$modalButton).on('click', function (event) {
        return _this.handleModalClose(event);
      });
      this.elements.$notifyModalWrapper.on('click', function (event) {
        return _this.handleModalOutsideClick(event);
      });
      this.elements.$notifyModalWrapper.removeAttr('style');
      this.elements.$curriculumPreview.css('display', 'none');
      this.elements.$countdown.each(function () {
        var $time = jQuery(this).data('timer');
        if ($time <= 0 || new Date() > $time) return;
        jQuery(this).countdown({
          timestamp: jQuery(this).data('timer')
        });
      });
    }
  }, {
    key: "handleNotifyAlertClick",
    value: function handleNotifyAlertClick() {
      var _this2 = this;
      this.elements.$notifyAlertButton.toggleClass('notify-me');
      this.courseId = this.getElementSettings('course');
      if (!coming_soon.is_logged) {
        this.elements.$notifyContainer.css('display', this.elements.$notifyContainer.css('display') === 'none' ? 'flex' : 'none');
      }
      jQuery.ajax({
        type: 'POST',
        url: coming_soon.url,
        data: {
          action: 'coming_soon_notify_me',
          email: '',
          nonce: coming_soon.nonce,
          id: this.courseId
        },
        success: function success(response) {
          if (response.success) {
            _this2.elements.$notifyAlertButton.addClass('added-email');
            _this2.elements.$notifyModalWrapper.addClass('masterstudy-coming-soon-modal_active');
            jQuery('body').addClass('masterstudy-coming-soon-popup');
            _this2.elements.$notifyModalWrapper.find('.masterstudy-coming-soon-modal__title').text(response.title);
            _this2.elements.$notifyModalWrapper.find('.masterstudy-coming-soon-modal__description').text(response.description);
          }
        }
      });
    }
  }, {
    key: "handleInputChange",
    value: function handleInputChange(event) {
      jQuery(event.currentTarget).removeClass('coming-soon-notify-input_alert');
    }
  }, {
    key: "handleKeyPress",
    value: function handleKeyPress(event) {
      if (event.which === 13) {
        this.elements.$notifyMeBtn.trigger('click');
      }
    }
  }, {
    key: "handleNotifyMeClick",
    value: function handleNotifyMeClick(event) {
      var _this3 = this;
      event.preventDefault();
      var email = this.elements.$notifyInput.val();
      if (!this.isValidEmail(email)) {
        this.elements.$notifyContainer.addClass('validation-error');
        this.elements.$notifyInput.addClass('coming-soon-notify-input_alert');
        return;
      }
      this.elements.$notifyInput.removeClass('coming-soon-notify-input_alert');
      this.elements.$notifyContainer.removeClass('validation-error');
      this.courseId = this.getElementSettings('course');
      jQuery.ajax({
        type: 'POST',
        url: coming_soon.url,
        data: {
          action: 'coming_soon_notify_me',
          email: email,
          nonce: coming_soon.nonce,
          id: this.courseId
        },
        beforeSend: function beforeSend() {
          _this3.elements.$notifyMeBtn.addClass('masterstudy-button_loading');
        },
        success: function success(response) {
          if (response.success) {
            _this3.elements.$notifyAlertButton.addClass('added-email');
            _this3.elements.$notifyModalWrapper.addClass('masterstudy-coming-soon-modal_active');
            jQuery('body').addClass('masterstudy-coming-soon-popup');
            _this3.elements.$notifyModalWrapper.find('.masterstudy-coming-soon-modal__title').text(response.title);
            _this3.elements.$notifyModalWrapper.find('.masterstudy-coming-soon-modal__description').text(response.description);
          }
          _this3.elements.$notifyMeBtn.removeClass('masterstudy-button_loading');
        }
      });
    }
  }, {
    key: "handleModalClose",
    value: function handleModalClose(event) {
      event.preventDefault();
      this.elements.$notifyModalWrapper.removeClass('masterstudy-coming-soon-modal_active');
      jQuery('body').removeClass('masterstudy-coming-soon-popup');
      this.elements.$notifyContainer.css('display', 'none');
    }
  }, {
    key: "handleModalOutsideClick",
    value: function handleModalOutsideClick(event) {
      if (event.target === this.elements.$notifyModalWrapper[0]) {
        this.handleModalClose(event);
      }
    }
  }, {
    key: "isValidEmail",
    value: function isValidEmail(email) {
      var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
      return emailPattern.test(email);
    }
  }]);
  return CourseComingSoon;
}(elementorModules.frontend.handlers.Base);
jQuery(window).on('elementor/frontend/init', function () {
  var addHandler = function addHandler($element) {
    elementorFrontend.elementsHandler.addHandler(CourseComingSoon, {
      $element: $element
    });
  };
  elementorFrontend.hooks.addAction('frontend/element_ready/ms_lms_course_coming_soon.default', addHandler);
});