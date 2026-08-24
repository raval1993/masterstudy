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
var CourseBuyButton = /*#__PURE__*/function (_elementorModules$fro) {
  _inherits(CourseBuyButton, _elementorModules$fro);
  var _super = _createSuper(CourseBuyButton);
  function CourseBuyButton() {
    _classCallCheck(this, CourseBuyButton);
    return _super.apply(this, arguments);
  }
  _createClass(CourseBuyButton, [{
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      return {
        selectors: {
          buyButton: '.masterstudy-buy-button',
          buyButtonDropdown: '.masterstudy-buy-button-dropdown',
          buyButtonDisabled: '.masterstudy-buy-button__link_disabled',
          prerequisitesButton: '.masterstudy-prerequisites__button',
          prerequisitesContainer: '.masterstudy-prerequisites',
          explanationTitle: '.masterstudy-prerequisites-list__explanation-title',
          dropdownSectionHead: '.masterstudy-buy-button-dropdown__head',
          bodyWrapper: '.masterstudy-buy-button-dropdown__body-wrapper',
          membershipPlanLink: '.masterstudy-membership-plan-link',
          membershipPlanLinkUse: '.masterstudy-membership-plan-link_use',
          membershipPlanButton: '.masterstudy-membership-plan__button'
        }
      };
    }
  }, {
    key: "getDefaultElements",
    value: function getDefaultElements() {
      var selectors = this.getSettings('selectors');
      return {
        $buyButton: this.$element.find(selectors.buyButton),
        $buyButtonDropdown: this.$element.find(selectors.buyButtonDropdown),
        $buyButtonDisabled: this.$element.find(selectors.buyButtonDisabled),
        $prerequisitesButton: this.$element.find(selectors.prerequisitesButton),
        $prerequisitesContainer: this.$element.find(selectors.prerequisitesContainer),
        $explanationTitle: this.$element.find(selectors.explanationTitle),
        $dropdown: this.$element.find(selectors.buyButtonDropdown),
        $dropdownSectionHead: this.$element.find(selectors.dropdownSectionHead),
        $bodyWrapper: this.$element.find(selectors.bodyWrapper),
        $membershipPlanLink: this.$element.find(selectors.membershipPlanLink),
        $membershipPlanLinkUse: this.$element.find(selectors.membershipPlanLinkUse),
        $membershipPlanButton: this.$element.find(selectors.membershipPlanButton)
      };
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      var _this = this;
      if (this.elements.$buyButton.length) {
        this.elements.$buyButton.on('click', function () {
          return _this.toggleDropdown();
        });
      }
      if (this.elements.$buyButtonDropdown.length) {
        this.elements.$buyButtonDropdown.on('click', function (event) {
          return event.stopPropagation();
        });
      }
      if (this.elements.$buyButtonDisabled.length) {
        this.elements.$buyButtonDisabled.on('click', function (event) {
          return event.preventDefault();
        });
      }
      if (this.elements.$prerequisitesButton.length) {
        this.elements.$prerequisitesButton.on('click', function (event) {
          return _this.togglePrerequisites(event);
        });
      }
      if (this.elements.$explanationTitle.length) {
        this.elements.$explanationTitle.on('click', function (event) {
          return _this.toggleExplanation(event);
        });
      }
      if (this.elements.$dropdown.length) {
        var s = this.getSettings('selectors');
        var openClass = 'masterstudy-buy-button-dropdown__section_open';
        this.elements.$dropdown.off('click.msbHead');
        this.elements.$dropdown.on('click.msbHead', s.dropdownSectionHead, function (event) {
          event.preventDefault();
          event.stopPropagation();
          var $head = jQuery(event.currentTarget);
          var $section = $head.closest('.masterstudy-buy-button-dropdown__section');
          var $dropdown = $head.closest(s.buyButtonDropdown);
          $dropdown.find('.masterstudy-buy-button-dropdown__section').not($section).removeClass(openClass);
          $section.toggleClass(openClass);
        });
      }
      if (this.elements.$dropdown.length) {
        var _s = this.getSettings('selectors');
        this.elements.$dropdown.off('click.msbMembership');
        this.elements.$dropdown.on('click.msbMembership', _s.membershipPlanLink, function (event) {
          event.preventDefault();
          event.stopPropagation();
          var $link = jQuery(event.currentTarget);
          var $wrap = $link.closest(_s.bodyWrapper);
          $wrap.find(_s.membershipPlanLinkUse).removeClass('masterstudy-membership-plan-link_use');
          $link.addClass('masterstudy-membership-plan-link_use');
          $wrap.find(_s.membershipPlanButton).removeClass('masterstudy-membership-plan__button_disabled');
        });
      }
      jQuery(document).on('click', function (event) {
        return _this.handleOutsideClick(event);
      });
    }
  }, {
    key: "toggleDropdown",
    value: function toggleDropdown() {
      this.animationActive = !this.animationActive;
      this.elements.$buyButton.toggleClass('dropdown-show', this.animationActive);
    }
  }, {
    key: "togglePrerequisites",
    value: function togglePrerequisites(event) {
      event.preventDefault();
      jQuery(event.currentTarget).parent().toggleClass('active');
    }
  }, {
    key: "toggleExplanation",
    value: function toggleExplanation(event) {
      event.preventDefault();
      jQuery(event.currentTarget).parent().toggleClass('active');
    }
  }, {
    key: "handleOutsideClick",
    value: function handleOutsideClick(event) {
      if (!jQuery(event.target).closest('.masterstudy-buy-button').length && this.animationActive) {
        this.toggleDropdown();
      }
      if (!jQuery(event.target).closest('.masterstudy-prerequisites').length) {
        this.elements.$prerequisitesContainer.removeClass('active');
      }
    }
  }]);
  return CourseBuyButton;
}(elementorModules.frontend.handlers.Base);
jQuery(window).on('elementor/frontend/init', function () {
  var addHandler = function addHandler($element) {
    elementorFrontend.elementsHandler.addHandler(CourseBuyButton, {
      $element: $element
    });
  };
  elementorFrontend.hooks.addAction('frontend/element_ready/ms_lms_course_buy_button.default', addHandler);
});