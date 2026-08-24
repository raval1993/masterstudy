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
var ReviewsComponent = /*#__PURE__*/function (_elementorModules$fro) {
  _inherits(ReviewsComponent, _elementorModules$fro);
  var _super = _createSuper(ReviewsComponent);
  function ReviewsComponent() {
    _classCallCheck(this, ReviewsComponent);
    return _super.apply(this, arguments);
  }
  _createClass(ReviewsComponent, [{
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      return {
        selectors: {
          loadMoreButton: "[data-id='masterstudy-single-course-reviews-more']",
          reviewsList: '.masterstudy-single-course-reviews__list-wrapper',
          openButton: '.masterstudy-single-course-reviews__add-button',
          closeButton: '.masterstudy-single-course-reviews__form-close',
          reviewForm: '.masterstudy-single-course-reviews__form',
          reviewFormStars: '.masterstudy-single-course-reviews__star',
          errorMessageBlock: '.masterstudy-single-course-reviews__form-message',
          needContentBanner: '.masterstudy-elementor-need-content-banner_reviews'
        }
      };
    }
  }, {
    key: "getDefaultElements",
    value: function getDefaultElements() {
      var selectors = this.getSettings('selectors');
      return {
        $loadMoreButton: this.$element.find(selectors.loadMoreButton),
        $reviewsList: this.$element.find(selectors.reviewsList),
        $openButton: this.$element.find(selectors.openButton),
        $closeButton: this.$element.find(selectors.closeButton),
        $reviewForm: this.$element.find(selectors.reviewForm),
        $reviewFormStars: this.$element.find(selectors.reviewFormStars),
        $errorMessageBlock: this.$element.find(selectors.errorMessageBlock),
        $needContentBanner: this.$element.find(selectors.needContentBanner)
      };
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      jQuery(document).ready(this.initReviews.bind(this));
      this.elements.$openButton.on('click', this.showAddReviewForm.bind(this));
      this.elements.$closeButton.on('click', this.closeReviewForm.bind(this));
      this.elements.$reviewFormStars.on('click', this.handleStarClick.bind(this));
    }
  }, {
    key: "showAddReviewForm",
    value: function showAddReviewForm(event) {
      event.preventDefault();
      this.elements.$reviewForm.addClass('masterstudy-single-course-reviews__form_active');
    }
  }, {
    key: "closeReviewForm",
    value: function closeReviewForm(event) {
      event.preventDefault();
      this.elements.$errorMessageBlock.removeClass('masterstudy-single-course-reviews__form-message_active').html('');
      this.elements.$reviewForm.removeClass('masterstudy-single-course-reviews__form_active');
      this.elements.$reviewFormStars.removeClass('masterstudy-single-course-reviews__star_clicked');
    }
  }, {
    key: "handleStarClick",
    value: function handleStarClick(event) {
      var $clickedStar = jQuery(event.currentTarget);
      $clickedStar.addClass('masterstudy-single-course-reviews__star_clicked');
      $clickedStar.siblings().removeClass('masterstudy-single-course-reviews__star_clicked');
      $clickedStar.prevAll().addBack().addClass('masterstudy-single-course-reviews__star_clicked');
    }
  }, {
    key: "initReviews",
    value: function initReviews() {
      this.offset = 0;
      this.total = true;
      this.reviewText = '';
      this.userMark = '';
      this.courseId = this.getElementSettings('course');
      if (typeof reviews_data !== 'undefined') {
        this.getReviews();
      }
    }
  }, {
    key: "getReviews",
    value: function getReviews() {
      var _this = this;
      var preset = this.getElementSettings('preset');
      var pp = preset === 'grid' ? 6 : 5;
      var getReviewsUrl = "".concat(stm_lms_ajaxurl, "?action=stm_lms_get_reviews&nonce=").concat(stm_lms_nonces['stm_lms_get_reviews'], "&offset=").concat(this.offset, "&pp=").concat(pp) + (this.courseId ? "&post_id=".concat(this.courseId) : '');
      var reviewHtml = '';
      this.elements.$loadMoreButton.addClass('masterstudy-button_loading');
      jQuery.get(getReviewsUrl, function (response) {
        if (response.posts.length > 0) {
          response.posts.forEach(function (review) {
            reviewHtml += _this.generateReviewHtml(review);
          });
          _this.elements.$reviewsList.html(_this.elements.$reviewsList.html() + reviewHtml);
          _this.offset++;
          _this.total = response.total;
        } else {
          _this.elements.$needContentBanner.show();
        }
        _this.elements.$loadMoreButton.removeClass('masterstudy-button_loading');
        _this.total ? _this.elements.$loadMoreButton.parent().hide() : _this.elements.$loadMoreButton.parent().show();
      });
    }
  }, {
    key: "generateReviewHtml",
    value: function generateReviewHtml(review) {
      var starsHtml = '';
      for (var i = 1; i <= 5; i++) {
        starsHtml += "<span class=\"masterstudy-single-course-reviews__star".concat(i <= review.mark ? ' masterstudy-single-course-reviews__star_filled' : '', "\"></span>");
      }
      return "\n            <div class=\"masterstudy-single-course-reviews__item\">\n                <div class=\"masterstudy-single-course-reviews__item-header\">\n                    <div class=\"masterstudy-single-course-reviews__item-mark\">".concat(starsHtml, "</div>\n                    ").concat(review.status === 'pending' ? "<div class=\"masterstudy-single-course-reviews__item-status\">".concat(reviews_data.status, "</div>") : '', "\n                </div>\n                <div class=\"masterstudy-single-course-reviews__item-content\">").concat(review.content, "</div>\n                <div class=\"masterstudy-single-course-reviews__item-row\">\n                    <a class=\"masterstudy-single-course-reviews__item-user\"\n                        ").concat(reviews_data.student_public_profile ? "href=\"".concat(review.user_url, "\"") : '', "\n                    >\n                        <span class=\"masterstudy-single-course-reviews__item-author\">").concat(reviews_data.author_label, "</span>\n                        <span class=\"masterstudy-single-course-reviews__item-author-name\">").concat(review.user, "</span>\n                    </a>\n                    <div class=\"masterstudy-single-course-reviews__item-date\">").concat(review.time, "</div>\n                </div>\n            </div>");
    }
  }]);
  return ReviewsComponent;
}(elementorModules.frontend.handlers.Base);
jQuery(window).on('elementor/frontend/init', function () {
  var addHandler = function addHandler($element) {
    elementorFrontend.elementsHandler.addHandler(ReviewsComponent, {
      $element: $element
    });
  };
  elementorFrontend.hooks.addAction('frontend/element_ready/ms_lms_course_reviews.default', addHandler);
});